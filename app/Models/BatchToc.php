<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchToc extends Model
{
    protected $table = 'batch_tocs';

    protected $fillable = [
        'batch_id',
        'course_id',
        'trainer_id',
        'title',
        'plan_start_date',
        'plan_end_date',
        'actual_start_date',
        'actual_end_date',
        'remark_admin',
        'remark_trainer',
        'status',
        'percentage',
        'created_by',
        'updated_by'
    ];

    public static function statuses(): array
    {
        return [
            'planned'     => 'Planned',
            'in_progress' => 'In Progress',
            'on_hold'     => 'On Hold',
            'completed'   => 'Completed',
        ];
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class);
    }

    public function trainer()
    {
        return $this->belongsTo(\App\Models\User::class, 'trainer_id');
    }
}
