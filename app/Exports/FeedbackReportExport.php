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

            $categories = collect($row->details)
                ->pluck('category')
                ->filter()
                ->unique()
                ->implode(', ');

            return [
                'Batch' => $row->batch->name ?? '-',
                'Learner' => $row->learner->name ?? '-',
                'Trainer' => $row->trainer->name ?? '-',
                'Type' => ucfirst($row->type ?? '-'),
                'Category' => $categories ?: '-',
                'Avg Score' => $row->avg_score ?? '-',
                'Date' => optional($row->created_at)->format('d-m-Y H:i') ?? '-'
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
            'Date'
        ];
    }
}
