<?php

namespace App\Http\Controllers\Session;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SessionAuthorization;
use App\Mail\SessionAttendanceMail;
use App\Models\BatchSession;
use App\Models\SessionAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SessionAttendanceController extends Controller
{
    use SessionAuthorization;

    public function index(BatchSession $session)
    {
        $this->authorizeSession($session);

        // Attendance can only be marked on or after session date
        if (Carbon::parse($session->start_date)->isFuture()) {
            abort(403, 'Attendance can only be marked on or after session date.');
        }

        // Get learners of the batch
        $learners = $session->batch
            ->learners()
            ->orderBy('name', 'asc')
            ->get();

        // Existing attendance keyed by learner_id
        $attendance = SessionAttendance::where('session_id', $session->id)
            ->get()
            ->keyBy('learner_id');

        return view('sessions.attendance.index', compact(
            'session',
            'learners',
            'attendance'
        ));
    }

    public function store(Request $request, BatchSession $session)
    {
        $this->authorizeSession($session);

        // Only Admin / Trainer
        if (!in_array(Auth::user()->role, ['admin', 'trainer'])) {
            abort(403);
        }

        if (Carbon::parse($session->start_date)->isFuture()) {
            return back()->with('error', 'Attendance cannot be marked before session date.');
        }

        $request->validate([
            'attendance' => 'required|array',
        ]);

        foreach ($request->attendance as $learnerId => $data) {

            $isPresent = isset($data['present']) ? 'present' : 'absent';

            SessionAttendance::updateOrCreate(
                [
                    'session_id' => $session->id,
                    'learner_id' => $learnerId,
                ],
                [
                    'present'     => $isPresent,
                    'late_entry'  => isset($data['late_entry']) ? 'yes' : 'no',
                    'early_exit'  => isset($data['early_exit']) ? 'yes' : 'no',
                    'remarks'     => $data['remarks'] ?? null,
                    'marked_at'   => now(),
                    'marked_by'   => Auth::id(),
                    'source'      => 'trainer',
                ]
            );
        }

        return back()->with('success', 'Attendance saved successfully.');
    }

    public function sendAttendanceEmail(BatchSession $session)
    {
        $this->authorizeSession($session);

        $customerEmail = $session->batch->customer?->email;
        $trainerEmail  = $session->trainer?->email;

        if (!$customerEmail && !$trainerEmail) {
            return back()->with('error', 'Customer or trainer email not found.');
        }

        // Get tenant/client Code properly 
        $clientCode = session('client_code'); // or $session->batch->client_code if you prefer

        if (!empty($customerEmail)) {

            Mail::to($customerEmail)
                ->cc(array_filter([$trainerEmail]))
                ->later(
                    now()->addSeconds(10),
                    new SessionAttendanceMail($session, $clientCode)
                );
        }

        return back()->with('success', 'Attendance email will be sent.');
    }
}
