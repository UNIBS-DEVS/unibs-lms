<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchFeedbackQuestion extends Model
{
    protected $fillable = [
        'batch_id',
        'question',
        'type',
    ];

    /* 🔗 Relationships */

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
