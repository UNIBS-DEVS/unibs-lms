<?php

namespace App\Http\Controllers\Reports;

use App\Models\Batch;
use App\Models\SessionAttendance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\AttendanceReportMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceReportExport;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $batches = Batch::orderBy('name')->get();
        } elseif ($user->role === 'trainer') {
            $batches = Batch::whereHas('trainers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
                ->orderBy('name')
                ->get();
        } elseif ($user->role === 'learner') {
            $batches = Batch::whereHas('learners', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
                ->orderBy('name')
                ->get();
        } else {
            $batches = Batch::where('customer_id', $user->id)
                ->orderBy('name')
                ->get();
        }

        return view('reports.attendance.index', [
            'batches' => $batches,
            'attendances' => collect(),
            'summary' => [
                'present' => 0,
                'absent' => 0,
                'late_entry' => 0,
                'early_exit' => 0,
            ],
        ]);
    }

    private function filteredQuery(Request $request)
    {
        $user = Auth::user();

        $query = SessionAttendance::with([
            'learner:id,name',
            'session:id,batch_id,session_name,start_date,start_time',
            'session.batch:id,name',
            'session.course:id,name',
            'session.trainer:id,name',
        ]);

        // ✅ Batch filter
        if ($request->filled('batch_id')) {
            $query->whereHas('session', fn($q) => $q->where('batch_id', $request->batch_id));
        }

        // ✅ Date filters
        if ($request->filled('from_date')) {
            $query->whereHas('session', fn($q) => $q->whereDate('start_date', '>=', $request->from_date));
        }

        if ($request->filled('to_date')) {
            $query->whereHas('session', fn($q) => $q->whereDate('start_date', '<=', $request->to_date));
        }

        if ($user->role === 'trainer') {
            $query->whereHas('session.batch.trainers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        // ✅ Status filter
        if ($request->status === 'present') {
            $query->where('is_present', 1);
        } elseif ($request->status === 'absent') {
            $query->where('is_present', 0);
        }

        // 🔥 IMPORTANT: Learner restriction
        if ($user->role === 'learner') {
            $query->where('learner_id', $user->id);
        }

        return $query;
    }

    // 🔹 AJAX Filter
    public function filter(Request $request)
    {
        $request->validate([
            'batch_id' => 'required',
        ]);

        $query = $this->filteredQuery($request)
            ->with(['session.batch', 'session.course', 'session.trainer', 'learner']);


        $attendances = $query->latest('marked_at')->get();

        $summary = [
            'total' => $attendances->count(),
            'present' => $attendances->where('is_present', 1)->count(),
            'absent' => $attendances->where('is_present', 0)->count(),
            'late_entry' => $attendances->where('late_entry', 1)->count(),
            'early_exit' => $attendances->where('early_exit', 1)->count(),
        ];

        $data = $attendances->map(function ($item) {
            return [
                'session_name' => $item->session->session_name ?? '-',
                'batch_name' => $item->session->batch->name ?? '-',
                'session_date' => $item->session->start_date
                    ? \Carbon\Carbon::parse($item->session->start_date)->format('d M Y') . ' ' .
                    \Carbon\Carbon::parse($item->session->start_time)->format('H:i:s')
                    : '-',
                'learner_name' => $item->learner->name ?? '-',
                'present' => $item->is_present ? 'present' : 'absent',
                'late_entry' => $item->late_entry ? 'yes' : 'no',
                'early_exit' => $item->early_exit ? 'yes' : 'no',
                'marked_at' => $item->marked_at
                    ? \Carbon\Carbon::parse($item->marked_at)->format('d M Y, H:i:s')
                    : '-',
            ];
        });

        return response()->json([
            'data' => $data,
            'summary' => $summary,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        $attendances = $this->filteredQuery($request)
            ->with(['session.batch', 'session.course', 'session.trainer', 'learner'])
            ->get();

        $clientCode = session('client_code');

        // ✅ Get Batch Name
        $batchName = null;
        if ($request->filled('batch_id')) {
            $batch = Batch::find($request->batch_id);
            $batchName = $batch?->name;
        }

        // ✅ Get Course Name
        $courseName = $attendances->pluck('session.course.name')->filter()->unique()->implode(', ');

        if (!empty($user->email)) {
            Mail::to($user->email)->later(
                now()->addSeconds(5),
                new AttendanceReportMail(
                    $attendances->toArray(),
                    $user,
                    $batchName,
                    $courseName,
                    'excel',
                    $clientCode
                )
            );
        }

        return Excel::download(
            new AttendanceReportExport($attendances),
            'Attendance Report-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        $attendances = $this->filteredQuery($request)
            ->with(['session.batch', 'session.course', 'session.trainer', 'learner'])
            ->get();

        $clientCode = session('client_code');

        // ✅ Get Batch Name
        $batchName = null;
        if ($request->filled('batch_id')) {
            $batch = Batch::find($request->batch_id);
            $batchName = $batch?->name;
        }

        // ✅ Get Course Name
        $courseName = optional($attendances->first()?->session?->course)->name;

        if (!empty($user->email)) {
            Mail::to($user->email)->later(
                now()->addSeconds(5),
                new AttendanceReportMail(
                    $attendances->toArray(),
                    $user,
                    $batchName,
                    $courseName,
                    'pdf',
                    $clientCode
                )
            );
        }

        return Pdf::loadView('reports.attendance.pdf', [
            'attendances' => $attendances,
            'batchName' => $batchName,
            'courseName' => $courseName
        ])
            ->setPaper('a4', 'landscape')
            ->download('attendance-report.pdf');
    }
}
