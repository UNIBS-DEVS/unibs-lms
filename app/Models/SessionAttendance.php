<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\BatchSession;

class SessionAttendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'session_id',
        'learner_id',
        'is_present',
        'late_entry',    // 'yes' | 'no'
        'early_exit',    // 'yes' | 'no'
        'marked_at',
        'marked_by',
        'source',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_present' => 'boolean',
        'late_entry' => 'boolean',
        'early_exit' => 'boolean',
        'marked_at'  => 'datetime',
    ];

    /* -----------------------------------------------------------------
     | Relationships
     |------------------------------------------------------------------*/

    public function learner()
    {
        return $this->belongsTo(User::class, 'learner_id');
    }

    public function session()
    {
        return $this->belongsTo(BatchSession::class, 'session_id');
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /* -----------------------------------------------------------------
     | Optional Helper Methods (Clean, Boolean-style access)
     |------------------------------------------------------------------*/

    public function isPresent(): bool
    {
        return $this->is_present;
    }

    public function isLate(): bool
    {
        return $this->late_entry;
    }

    public function isEarlyExit(): bool
    {
        return $this->early_exit;
    }
}
