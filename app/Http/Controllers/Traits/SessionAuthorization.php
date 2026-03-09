<?php

namespace App\Http\Controllers\Traits;

use App\Models\Batch;
use App\Models\BatchSession;
use Illuminate\Support\Facades\Auth;

trait SessionAuthorization
{
    protected function authorizeSession(BatchSession $session): void
    {
        $user = Auth::user();

        // Admin = full access
        if ($user->role === 'admin') {
            return;
        }

        // Trainer = must belong to batch trainers
        if (
            $user->role === 'trainer' &&
            $session->batch?->trainers()->where('users.id', $user->id)->exists()
        ) {
            return;
        }

        abort(403, 'You cannot access sessions of another trainer');
    }


    protected function authorizeBatch(Batch $batch)
    {
        $user = Auth::user();

        if (
            $user->role === 'trainer' &&
            !$batch->trainers()->where('users.id', $user->id)->exists()
        ) {
            abort(403);
        }
    }
}
