<?php

namespace App\Http\Controllers\Batch;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Http\Request;

class BatchLearnerController extends Controller
{
    public function index()
    {
        $batches = Batch::where('status', 'active')
            ->withCount('learners')
            ->with(['courses', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('batch_learners.index', compact('batches'));
    }

    public function show(string $id)
    {
        $batch = Batch::with([
            'courses',
            'customer',
            'learners' => function ($q) {
                $q->orderBy('name');
            }
        ])
            ->withCount('learners')
            ->findOrFail($id);

        return view('batch_learners.show', compact('batch'));
    }

    public function edit(string $id)
    {
        $batch = Batch::with('learners')->findOrFail($id);

        // All active learners
        $allLearners = User::where('role', 'learner')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Learners already in this batch
        $assignedLearners = $batch->learners;

        // Learners NOT in this batch (for left box)
        $availableLearners = $allLearners->whereNotIn(
            'id',
            $assignedLearners->pluck('id')
        );

        return view('batch_learners.edit', compact(
            'batch',
            'availableLearners',
            'assignedLearners'
        ));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'learners'   => 'nullable|array',
            'learners.*' => 'exists:users,id',
        ]);

        $batch = Batch::findOrFail($id);

        // Sync learners (add + remove)
        $batch->learners()->sync($request->learners ?? []);

        return redirect()
            ->route('batch-learners.index')
            ->with('success', 'Batch learners updated successfully.');
    }
}
