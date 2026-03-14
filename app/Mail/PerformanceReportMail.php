<?php

namespace App\Mail;

use App\Exports\PerformanceReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;

class PerformanceReportMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $data;
    private $user;
    private $type;
    private $batchName;
    private $startDate;
    private $endDate;
    protected string $clientCode;

    public function __construct($data, $user, $type, $batchName, $startDate, $endDate, $clientCode)
    {
        $this->data = $data;
        $this->user = $user;
        $this->type = $type;
        $this->batchName = $batchName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->clientCode = $clientCode;
    }

    public function build()
    {
        $this->configureTenantMail();

        $mail = $this->subject('Performance Report')
            ->view('emails.performance-report', [
                'user' => $this->user
            ]);

        if ($this->type === 'excel') {

            $excel = Excel::raw(
                new PerformanceReportExport(
                    collect($this->data),
                    $this->batchName,
                    $this->startDate,
                    $this->endDate
                ),
                ExcelExcel::XLSX
            );

            $mail->attachData(
                $excel,
                'Performance Report.xlsx',
                ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
        }

        if ($this->type === 'pdf') {

            $pdf = Pdf::loadView('reports.performance.pdf', [
                'performances' => $this->data,
                'batchName' => $this->batchName,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate
            ]);

            $mail->attachData(
                $pdf->output(),
                'Performance Report.pdf',
                ['mime' => 'application/pdf']
            );
        }

        return $mail;
    }
}
