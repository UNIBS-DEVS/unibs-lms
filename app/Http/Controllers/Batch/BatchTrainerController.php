<?php

namespace App\Http\Controllers\Batch;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Http\Request;

class BatchTrainerController extends Controller
{
    public function index()
    {
        $batches = Batch::where('status', 'active')
            ->withCount('trainers')
            ->with(['courses', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('batch_trainers.index', compact('batches'));
    }

    public function show(string $id)
    {
        $batch = Batch::with([
            'courses',
            'customer',
            'trainers' => function ($q) {
                $q->orderBy('name');
            }
        ])
            ->withCount('trainers')
            ->findOrFail($id);

        return view('batch_trainers.show', compact('batch'));
    }

    public function edit(string $id)
    {
        $batch = Batch::with('trainers')->findOrFail($id);

        // All active trainers
        $allTrainers = User::where('role', 'trainer')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Assigned Trainers (already in batch)
        $assignedTrainers = $batch->trainers;

        // Trainers NOT in this batch
        $availableTrainers = $allTrainers->whereNotIn(
            'id',
            $assignedTrainers->pluck('id')
        );

        return view('batch_trainers.edit', compact(
            'batch',
            'availableTrainers',
            'assignedTrainers'
        ));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'trainers'   => 'nullable|array',
            'trainers.*' => 'exists:users,id',
        ]);

        $batch = Batch::findOrFail($id);

        // Sync trainers
        $batch->trainers()->sync($request->trainers ?? []);

        return redirect()
            ->route('batch-trainers.index')
            ->with('success', 'Batch trainers updated successfully.');
    }
}
