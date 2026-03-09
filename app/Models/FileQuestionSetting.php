<?php

namespace App\Models;

use App\Models\Question;
use Illuminate\Database\Eloquent\Model;

class FileQuestionSetting extends Model
{
    protected $fillable = [
        'question_id',
        'allowed_file_types',
        'max_file_size_mb',
        'upload_path',
    ];
}
