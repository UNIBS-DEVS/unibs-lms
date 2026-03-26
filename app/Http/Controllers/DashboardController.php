<?php

namespace App\Http\Controllers;

use App\Models\Batch;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $batches = [];

        if ($user->role === 'learner') {
            $batches = Batch::whereHas('learners', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })->get();
        }

        return view('dashboard.index', compact('user', 'batches'));
    }
}
