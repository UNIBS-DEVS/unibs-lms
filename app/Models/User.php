<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Batch;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'role',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function learnerBatches()
    {
        return $this->belongsToMany(
            Batch::class,
            'batch_learners',
            'learner_id',
            'batch_id'
        );
    }

    public function trainerBatches()
    {
        return $this->belongsToMany(
            Batch::class,
            'batch_trainers',
            'trainer_id',
            'batch_id'
        );
    }

    public function batches()
    {
        if ($this->role === 'learner') {
            return $this->learnerBatches();
        }

        if ($this->role === 'trainer') {
            return $this->trainerBatches();
        }

        return Batch::query()->whereRaw('1=0');
    }
}
