<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FeedbackReportExport implements FromCollection, WithHeadings
{
    private $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($row) {

            $categories = collect($row['details'] ?? [])
                ->pluck('category')
                ->filter()
                ->unique()
                ->implode(', ');

            // convert avg score to percentage
            $avgScore = !empty($row['avg_score'])
                ? round(($row['avg_score'] / 5) * 100, 2) . '%'
                : '-';

            return [
                'Batch'      => $row['batch']['name'] ?? '-',
                'Learner'    => $row['learner']['name'] ?? '-',
                'Trainer'    => $row['trainer']['name'] ?? '-',
                'Type'       => !empty($row['type']) ? ucfirst($row['type']) : '-',
                'Category'   => $categories ?: '-',
                'Avg Score'  => $avgScore,
                'Date'       => !empty($row['created_at'])
                    ? \Carbon\Carbon::parse($row['created_at'])->format('d-m-Y H:i')
                    : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Batch',
            'Learner',
            'Trainer',
            'Type',
            'Category',
            'Avg Score',
            'Date',
        ];
    }
}
