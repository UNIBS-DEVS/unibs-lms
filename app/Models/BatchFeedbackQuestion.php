<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchFeedbackQuestion extends Model
{
    protected $fillable = [
        'batch_id',
        'question',
        'category',
        'type',
    ];

    /* 🔗 Relationships */

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function defaultFeedback()
    {
        return $this->belongsTo(DefaultFeedback::class);
    }
}
