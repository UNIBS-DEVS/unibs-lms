<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchFbSubmissionDetail extends Model
{
    use HasFactory;

    protected $table = 'batch_fb_submission_details';

    protected $fillable = [
        'summery_id',
        'question',
        'score',
    ];

    public function summary()
    {
        return $this->belongsTo(BatchFbSummary::class, 'summery_id');
    }
}
