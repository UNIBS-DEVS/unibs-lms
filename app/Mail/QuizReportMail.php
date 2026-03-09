<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\QuizReportExport;

class QuizReportMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $attempts;
    public $user;
    public string $batchName;
    public string $quizName;
    public string $type;
    public string $clientCode;

    /**
     * Create a new message instance.
     */
    public function __construct(array $attempts, $user, $batchName, $quizName, string $type, $clientCode)
    {

        parent::__construct((string) $clientCode, 'code');

        $this->attempts = $attempts;
        $this->user = $user;
        $this->batchName = $batchName;
        $this->quizName = $quizName;
        $this->type = $type;
        $this->clientCode = (string) $clientCode;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $this->configureTenantMail();

        $mail = $this->subject('Quiz Report')
            ->view('emails.quiz-report', [
                'user' => $this->user
            ]);


        // Attach Excel
        if ($this->type === 'excel') {
            $excel = Excel::raw(
                new QuizReportExport(collect($this->attempts)),
                ExcelExcel::XLSX
            );

            $mail->attachData(
                $excel,
                'Quiz_Report.xlsx',
                ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
        }

        // Attach PDF
        if ($this->type === 'pdf') {
            $pdf = Pdf::loadView('reports.quiz.pdf', [
                // 'attempts' => $this->attempts,
                'attempts' => collect($this->attempts),
                'batchName' => $this->batchName,
                'quizName' => $this->quizName
            ])->setPaper('a4', 'landscape');


            $mail->attachData(
                $pdf->output(),
                'Quiz_Report.pdf',
                ['mime' => 'application/pdf']
            );
        }

        return $mail;
    }
}
