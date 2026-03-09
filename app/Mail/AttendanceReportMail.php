<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AttendanceReportExport;

class AttendanceReportMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $attendances;
    public $user;
    public string $batchName;
    public string $courseName;
    public string $type;
    public string $clientCode; // ✅ must be string

    public function __construct(array $attendances, $user, $batchName, $courseName, string $type, $clientCode)
    {
        parent::__construct((string) $clientCode, 'code');

        $this->attendances = $attendances;
        $this->user = $user;
        $this->batchName = $batchName;
        $this->courseName = $courseName;
        $this->type = $type;
        $this->clientCode = (string) $clientCode;
    }

    public function build()
    {
        $this->configureTenantMail();

        $mail = $this->subject('Attendance Report')
            ->view('emails.attendance-report', [
                'user' => $this->user
            ]);

        if ($this->type === 'pdf') {

            $pdf = Pdf::loadView(
                'reports.attendance.pdf',
                [
                    'attendances' => collect($this->attendances),
                    'batchName' => $this->batchName,
                    'courseName' => $this->courseName
                ]
            )->setPaper('a4', 'landscape');

            $mail->attachData(
                $pdf->output(),
                'Attendance Report.pdf',
                ['mime' => 'application/pdf']
            );
        }

        if ($this->type === 'excel') {

            $excel = Excel::raw(
                new AttendanceReportExport(collect($this->attendances)),
                ExcelExcel::XLSX
            );

            $mail->attachData(
                $excel,
                'Attendance Report.xlsx',
                ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
        }

        return $mail;
    }
}
