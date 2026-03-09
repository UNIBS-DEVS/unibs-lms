<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchFbSummary extends Model
{
    use HasFactory;

    protected $table = 'batch_fb_summaries'; // 👈 adjust if table name differs

    protected $fillable = [
        'batch_id',
        'trainer_id',
        'type',
        'submitted_by',
        'avg_score',
        'remarks',
    ];

    /* =======================
        Relationships
    ======================== */

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function learner()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function details()
    {
        return $this->hasMany(BatchFbSubmissionDetail::class, 'summery_id');
    }
}
