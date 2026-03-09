<?php

namespace App\Mail;

use App\Models\BatchSession;
use App\Models\SessionAttendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class SessionAttendanceMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public BatchSession $session;

    public function __construct(BatchSession $session, string $clientCode)
    {
        // Load SMTP using client_code from master DB
        parent::__construct($clientCode, 'code');

        // Load required relations BEFORE queue serialization
        $this->session = $session->load([
            'batch',
            'trainer',
            'course'
        ]);
    }

    public function build()
    {
        /* ===============================
           🔹 GET ATTENDANCE DATA
        ================================ */
        $attendance = SessionAttendance::with('learner')
            ->where('session_id', $this->session->id)
            ->get();

        /* ===============================
           🔹 GENERATE PDF
        ================================ */
        $pdf = Pdf::loadView('sessions.attendance.pdf', [
            'session'    => $this->session,
            'attendance' => $attendance
        ]);

        return $this->subject(
            'Session Attendance Report - ' . $this->session->session_name
        )
            ->view('emails.session_attendance')
            ->with([
                'session' => $this->session
            ])
            ->attachData(
                $pdf->output(),
                'session-attendance-report.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
