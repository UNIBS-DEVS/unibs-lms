<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\FeedbackReportExport;

class FeedbackReportMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $summaries;
    public $user;
    public string $type;
    public string $clientCode;
    public ?string $batchName;
    public ?string $feedbackType;

    public function __construct(
        array $summaries,
        $user,
        string $type,
        $clientCode,
        ?string $batchName = null,
        ?string $feedbackType = null
    ) {

        parent::__construct((string) $clientCode);

        $this->summaries = $summaries;
        $this->user = $user;
        $this->type = $type;
        $this->clientCode = $clientCode;
        $this->batchName = $batchName;
        $this->feedbackType = $feedbackType;
    }

    public function build()
    {
        $this->configureTenantMail();

        $mail = $this->subject('Feedback Report')
            ->view('emails.feedback-report', [
                'user' => $this->user
            ]);

        if ($this->type === 'excel') {

            $excel = Excel::raw(
                new FeedbackReportExport(collect($this->summaries)),
                ExcelExcel::XLSX
            );

            $mail->attachData(
                $excel,
                'feedback-report.xlsx',
                ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
        }

        if ($this->type === 'pdf') {

            $pdf = Pdf::loadView('reports.feedback.pdf', [
                'summaries' => collect($this->summaries),
                'batchName' => $this->batchName,
                'feedbackType' => $this->feedbackType
            ])->setPaper('a4', 'landscape');

            $mail->attachData(
                $pdf->output(),
                'feedback-report.pdf',
                ['mime' => 'application/pdf']
            );
        }

        return $mail;
    }
}
