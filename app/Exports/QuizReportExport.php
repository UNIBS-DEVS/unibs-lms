<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuizReportExport implements FromCollection, WithHeadings
{
    private $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // return collect($this->data)->map(function ($a) {

        return $this->data->map(function ($row) {

            // SUPPORT BOTH ARRAY + OBJECT
            $quiz = is_array($row) ? ($row['quiz'] ?? []) : ($row->quiz ?? null);
            $user = is_array($row) ? ($row['user'] ?? []) : ($row->user ?? null);

            $score = is_array($row)
                ? ($row['score'] ?? 0)
                : ($row->score ?? 0);

            $questions = is_array($quiz)
                ? ($quiz['questions'] ?? [])
                : ($quiz?->questions ?? []);

            $total = is_array($quiz)
                ? collect($quiz['questions'] ?? [])->sum('max_marks')
                : ($quiz?->questions->sum('max_marks') ?? 0);

            $percent = $total > 0
                ? round(($score / $total) * 100, 2)
                : null;

            $passPercent = is_array($quiz)
                ? ($quiz['minimum_passing_percentage'] ?? null)
                : ($quiz?->minimum_passing_percentage ?? null);

            $batch = is_array($quiz)
                ? ($quiz['batch']['name'] ?? '-')
                : ($quiz?->batch?->name ?? '-');

            $quizTitle = is_array($quiz)
                ? ($quiz['title'] ?? '-')
                : ($quiz?->title ?? '-');

            $learner = is_array($user)
                ? ($user['name'] ?? '-')
                : ($user?->name ?? '-');

            $status = is_array($row)
                ? ($row['status'] ?? '-')
                : ($row->status ?? '-');

            $started = is_array($row)
                ? ($row['started_at'] ?? null)
                : ($row->started_at ?? null);

            $completed = is_array($row)
                ? ($row['completed_at'] ?? null)
                : ($row->completed_at ?? null);

            return [
                'Batch'   => $batch,
                'Quiz'    => $quizTitle,
                'Learner' => $learner,
                'Status'  => $status,
                'Score'   => $score,
                'Total'   => $total ?: '-',
                'Percentage' => $percent !== null ? $percent . '%' : '-',
                'Result' => $percent !== null && $passPercent !== null
                    ? ($percent >= $passPercent ? 'Pass' : 'Fail')
                    : '-',
                'Started At' => $started
                    ? \Carbon\Carbon::parse($started)->format('d-m-Y H:i')
                    : '-',
                'Completed At' => $completed
                    ? \Carbon\Carbon::parse($completed)->format('d-m-Y H:i')
                    : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Batch',
            'Quiz',
            'Learner',
            'Status',
            'Score',
            'Total',
            'Percentage',
            'Result',
            'Started At',
            'Completed At',
        ];
    }
}
