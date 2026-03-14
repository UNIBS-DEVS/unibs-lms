<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class PerformanceReportExport implements FromCollection
{
    private $data;
    private $batchName;
    private $startDate;
    private $endDate;

    public function __construct($data, $batchName, $startDate, $endDate)
    {
        $this->data = $data;
        $this->batchName = $batchName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $rows = collect();

        /* Batch Name */
        $rows->push([
            'Batch : ' . ($this->batchName ?? '-'),
            '',
            '',
            '',
            '',
            ''
        ]);

        /* Date Range */
        $rows->push([
            'Date : ' . ($this->startDate ?? '-') . ' to ' . ($this->endDate ?? '-'),
            '',
            '',
            '',
            '',
            ''
        ]);

        /* Empty Row */
        $rows->push(['', '', '', '', '', '']);

        /* Table Headings */
        $rows->push([
            'Learner',
            'Attendance %',
            'Quiz %',
            'Feedback %',
            'Avg Score %',
            'Status'
        ]);

        /* Data Rows */
        foreach ($this->data as $row) {

            $rows->push([
                $row['learner'],
                $row['attendance'] . '%',
                $row['quiz'] . '%',
                $row['feedback'] . '%',
                $row['avg_score'] . '%',
                $row['status'],
            ]);
        }

        return $rows;
    }
}
