<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Traits\SessionAuthorization;
use App\Mail\BatchSessionCreatedMail;
use App\Mail\BatchSessionDeletedMail;
use App\Mail\BatchSessionUpdatedMail;
use App\Models\Batch;
use App\Models\BatchSession;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BatchSessionController extends Controller
{
    use SessionAuthorization;

    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        // ✅ default filter = today
        $filter = $request->get('filter', 'all');

        // $query = BatchSession::with('batch');
        $query = BatchSession::with(['batch', 'trainer', 'course']);

        // 🔐 Trainer: only own sessions
        if ($user->role === 'trainer') {
            $query->where('trainer_id', $user->id);
        }

        // 📅 Date  filter
        switch ($filter) {
            case 'today':
                $query->whereDate('start_date', Carbon::today());
                break;
            case 'past':
                $query->whereDate('start_date', '<', Carbon::today());
                break;
            case 'future':
                $query->whereDate('start_date', '>', Carbon::today());
                break;
            case 'all':
            default:
                break;
        }

        $sessions = $query->orderBy('start_date')->get();

        return view('batch_sessions.index', compact('sessions'));
    }

    public function create()
    {
        $user = Auth::user();

        $query = Batch::where('status', 'active');

        // Trainer: only own batches
        if ($user->role === 'trainer') {
            $query->whereHas('trainers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        // Admin: all batches 
        $batches = $query->with(['trainers', 'courses'])->get();

        return view('batch_sessions.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_name' => 'required|string|max:255',
            'batch_id'     => 'required|exists:batches,id',
            'trainer_id' => 'required|exists:users,id',
            'course_id'  => 'required|exists:courses,id',
            'start_date'   => 'required|date',
            'start_time'   => 'required',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'end_time'     => 'required',
            'location'     => 'nullable|string|max:255',
            'type'         => 'required|in:Online,Offline',
        ]);

        $user = Auth::user();
        $batch = Batch::with(['trainers', 'courses'])->findOrFail($request->batch_id);

        $this->authorizeBatch($batch);

        // 🔐 Trainer security check
        if ($user->role === 'trainer') {

            if (!$batch->trainers->contains('id', $user->id)) {
                abort(403, 'You are not allowed to create sessions for this batch.');
            }
        }

        if (!$batch->trainers->contains('id', $request->trainer_id)) {
            abort(403, 'Selected trainer not assigned to this batch.');
        }

        if (!$batch->courses->contains('id', $request->course_id)) {
            abort(403, 'Selected course not assigned to this batch.');
        }

        // ✅ Create session 
        $session = BatchSession::create($request->all());

        $session->load(['batch', 'trainer', 'course', 'batch.learners', 'batch.customer']);

        // 🔹 Collect learner emails
        $learnerEmails = $session->batch
            ->learners
            ->pluck('email')
            ->filter()
            ->toArray();

        // 🔹 Collect CC emails (customer + trainer)
        $ccEmails = collect([
            $session->batch->customer?->email,
            $session->trainer?->email,

        ])->filter()->toArray();

        $clientCode = session('client_code');

        // 🔹 Send ONE email
        if (!empty($learnerEmails)) {
            $mailData = [
                'session_name' => $session->session_name,
                'batch_name'   => $session->batch->name,
                'trainer_name' => $session->trainer?->name,
                'course_name'  => $session->course?->name,
                'start_date'   => $session->start_date,
                'start_time'   => $session->start_time,
                'end_date'     => $session->end_date,
                'end_time'     => $session->end_time,
                'location'     => $session->location,
                'type'         => $session->type,
            ];

            Mail::to($learnerEmails)
                ->cc($ccEmails)
                ->later(
                    now()->addSeconds(10),
                    new BatchSessionCreatedMail($mailData, $clientCode)
                );
        }

        return redirect()
            ->route('batch-sessions.index')
            ->with('success', 'Session created successfully & email sent');
    }

    public function show(string $id)
    {
        $session = BatchSession::with(['batch', 'trainer', 'course'])->findOrFail($id);
        $this->authorizeSession($session);

        return view('batch_sessions.show', compact('session'));
    }

    public function edit(string $id)
    {
        $session = BatchSession::findOrFail($id);
        $this->authorizeSession($session);

        $batches = Batch::where('status', 'active')
            ->with(['trainers', 'courses'])
            ->get();

        return view('batch_sessions.edit', compact('session', 'batches'));
    }

    public function update(Request $request, string $id)
    {
        $session = BatchSession::with(['batch', 'trainer', 'course', 'batch.learners', 'batch.customer'])
            ->findOrFail($id);
        $this->authorizeSession($session);

        // Validate input
        $validated = $request->validate([
            'session_name' => 'required|string|max:255',
            'batch_id'     => 'required|exists:batches,id',
            'trainer_id'   => 'required|exists:users,id',
            'course_id'    => 'required|exists:courses,id',
            'start_date'   => 'required|date',
            'start_time'   => 'required',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'end_time'     => 'required',
            'location'     => 'nullable|string|max:255',
            'type'         => 'required|in:Online,Offline',
        ]);

        // Capture original DB values
        $original = $session->getOriginal();

        // Update session
        $session->update($validated);

        // Detect changes (HUMAN READABLE)
        $changes = [];

        // dd($session->getChanges(), $original);
        foreach ($validated as $field => $newValue) {

            $oldValue = $original[$field] ?? null;

            // Convert IDs to human-readable names
            switch ($field) {
                case 'batch_id':
                    $oldValue = Batch::find($oldValue)?->name ?? '—';
                    $newValue = $session->batch?->name ?? '—';
                    $field = 'batch';
                    break;

                case 'trainer_id':
                    $oldValue = User::find($oldValue)?->name ?? '—';
                    $newValue = $session->trainer?->name ?? '—';
                    $field = 'trainer';
                    break;

                case 'course_id':
                    $oldValue = Course::find($oldValue)?->name ?? '—';
                    $newValue = $session->course?->name ?? '—';
                    $field = 'course';
                    break;
            }

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        // Send email only if something changed
        if (!empty($changes)) {
            // dd($changes);
            $mailData = [
                'session_name' => $session->session_name,
                'batch_name'   => $session->batch->name,
                'trainer_name' => $session->trainer?->name,
                'course_name'  => $session->course?->name,
                'start_date'   => $session->start_date,
                'start_time'   => $session->start_time,
                'end_date'     => $session->end_date,
                'end_time'     => $session->end_time,
                'location'     => $session->location,
                'type'         => $session->type,
                'changes'      => $changes,
            ];

            $learnerEmails = $session->batch->learners
                ->pluck('email')
                ->filter()
                ->toArray();

            $ccEmails = collect([
                $session->batch->customer?->email,
                $session->trainer?->email,
            ])->filter()->toArray();

            if (!empty($learnerEmails)) {
                $clientCode = session('client_code'); // or 'client_id' if you prefer

                Mail::to($learnerEmails)
                    ->cc($ccEmails)
                    ->later(
                        now()->addSeconds(10),
                        new BatchSessionUpdatedMail($mailData, $clientCode)
                    );
            }
        }

        return redirect()
            ->route('batch-sessions.index')
            ->with('success', 'Session updated successfully & email sent');
    }

    public function destroy(string $id)
    {
        $session = BatchSession::with([
            'batch.learners',
            'batch.customer',
            'batch',
            'trainer'
        ])->findOrFail($id);

        $this->authorizeSession($session);

        // Prepare mail data BEFORE delete
        $mailData = [
            'session_name' => $session->session_name,
            'batch_name'   => $session->batch->name,
            'course_name'  => $session->course?->name,
            'trainer_name' => $session->trainer?->name,
            'start_date'   => $session->start_date,
            'start_time'   => $session->start_time,
            'end_time'     => $session->end_time,
        ];

        $learnerEmails = $session->batch->learners
            ->pluck('email')
            ->filter()
            ->toArray();

        $ccEmails = collect([
            $session->batch->customer?->email,
            $session->trainer?->email,
        ])->filter()->toArray();

        $clientCode = session('client_code');

        // Delete session
        $session->delete();

        // Send mail
        if (!empty($learnerEmails)) {

            Mail::to($learnerEmails)
                ->cc($ccEmails)
                ->later(
                    now()->addSeconds(10),
                    new BatchSessionDeletedMail($mailData, $clientCode)
                );
        }

        return redirect()
            ->route('batch-sessions.index')
            ->with('success', 'Session deleted successfully & email sent');
    }
}
