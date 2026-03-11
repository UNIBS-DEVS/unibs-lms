<?php

// app/Models/DefaultFeedback.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultFeedback extends Model
{
    protected $table = 'default_feedbacks';

    protected $fillable = [
        'question',
        'type',
        'category',
    ];

    public function batchQuestions()
    {
        return $this->hasMany(BatchFeedbackQuestion::class);
    }
}
