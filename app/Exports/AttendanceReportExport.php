<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceReportExport implements FromCollection, WithHeadings
{
    private $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($row) {
            return [
                'Batch'        => $row['session']['batch']['name'] ?? '-',
                'Course'       => $row['session']['course']['name'] ?? '-',
                'Trainer'      => $row['session']['trainer']['name'] ?? '-',
                'Learner'      => $row['learner']['name'] ?? '-',
                'Session'      => $row['session']['session_name'] ?? '-',
                'Date'         => !empty($row['session']['start_date'])
                    ? \Carbon\Carbon::parse($row['session']['start_date'])->format('d M Y') . ' ' . \Carbon\Carbon::parse($row['session']['start_time'])->format('H:i:s')
                    : '-',
                'Status'       => $row['present'] === 'present' ? 'Present' : 'Absent',
                'Late Entry'   => $row['late_entry'] === 'yes' ? 'Yes' : '-',
                'Early Exit'   => $row['early_exit'] === 'yes' ? 'Yes' : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Batch',
            'Course',
            'Trainer',
            'Learner',
            'Session',
            'Date',
            'Status',
            'Late Entry',
            'Early Exit',
        ];
    }
}
