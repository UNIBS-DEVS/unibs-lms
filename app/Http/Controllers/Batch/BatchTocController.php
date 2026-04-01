<?php

namespace App\Http\Controllers\Batch;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchToc;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BatchTocController extends Controller
{
    public function index(Batch $batch)
    {
        $user = auth()->user();

        if ($user->role === 'learner') {
            $isAssigned = \App\Models\Batch::where('id', $batch->id)
                ->whereHas('learners', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->exists();

            if (!$isAssigned) {
                abort(403, 'Unauthorized batch access');
            }
        }

        $tocs = $batch->tocs()
            ->with(['course', 'trainer']) // ✅ eager load
            ->orderBy('planned_start_date')
            ->get();

        return view('batch_toc.index', compact('batch', 'tocs'));
    }
    
    public function create(Batch $batch)
    {
        $courses = $batch->courses; // ✅ only assigned courses

        $trainers = $batch->trainers; // batch assigned trainers

        return view('batch_toc.create', compact('batch', 'courses', 'trainers'));
    }

    public function store(Request $request, Batch $batch)
    {
        // dd('jhg');
        abort_unless(
            $batch->trainers()->where('trainer_id', $request->trainer_id)->exists(),
            403,
            'Trainer not assigned to this batch.'
        );

        $validated = $request->validate([
            'course_id'        => 'required|exists:courses,id',
            'trainer_id'       => 'required|exists:users,id',
            'title'            => 'required|string|max:255',
            'planned_start_date'  => 'required|date',
            'planned_end_date'    => 'required|date|after_or_equal:planned_start_date',
            'remark_admin'     => 'nullable|string',
        ]);

        $batch->tocs()->create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('batches.toc.index', $batch)
            ->with('success', 'Batch TOC added successfully.');
    }

    public function edit(Batch $batch, BatchToc $toc)
    {
        $courses = $batch->courses; // ✅ only assigned courses

        $trainers = $batch->trainers;

        return view('batch_toc.edit', compact('batch', 'toc', 'courses', 'trainers'));
    }

    public function update(Request $request, Batch $batch, BatchToc $toc)
    {
        abort_unless(
            $batch->trainers()->where('trainer_id', $request->trainer_id)->exists(),
            403,
            'Trainer not assigned to this batch.'
        );

        $validated = $request->validate([
            'course_id'        => 'required|exists:courses,id',
            'trainer_id'       => 'required|exists:users,id',
            'title'            => 'required|string|max:255',
            'planned_start_date'  => 'required|date',
            'planned_end_date'    => 'required|date|after_or_equal:planned_start_date',
            'remark_admin'     => 'nullable|string',
        ]);

        $toc->update([
            ...$validated,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('batches.toc.index', $batch)
            ->with('success', 'Batch TOC updated successfully.');
    }

    public function destroy(Batch $batch, BatchToc $toc)
    {
        $toc->delete();

        return redirect()
            ->route('batches.toc.index', $batch)
            ->with('success', 'Batch TOC deleted successfully.');
    } 
    
    public function progressIndex($batchId = null)
    {
        $user = auth()->user();
    
        $query = \App\Models\Batch::with('tocs');
    
        // 👇 If batchId is provided → filter only that batch
        if ($batchId) {
            $query->where('id', $batchId);
        }
    
        // 👇 Learner restriction
        if ($user->role === 'learner') {
            $query->whereHas('learners', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
    
        $batches = $query->get();
    
        return view('batch_toc.progress-index', compact('batches'));
    }
    // Trainer edit page
    public function progressEdit(Batch $batch, BatchToc $toc)
    {
        if (Auth::user()->role === 'trainer') {
            abort_unless(
                $batch->trainers()->where('trainer_id', Auth::id())->exists(),
                403
            );
        }

        return view('batch_toc.progress-edit', compact('batch', 'toc'));
    }

    // Trainer update
    public function progressUpdate(Request $request, Batch $batch, BatchToc $toc)
    {
        if (Auth::user()->role === 'trainer') {
            abort_unless(
                $batch->trainers()->where('trainer_id', Auth::id())->exists(),
                403
            );
        }

        $validated = $request->validate([
            'actual_start_date' => 'nullable|date',
            'actual_end_date'   => 'nullable|date|after_or_equal:actual_start_date',
            'remark_trainer'    => 'nullable|string',
            'status'            => 'required|in:planned,in_progress,on_hold,completed',
            'percentage'        => 'nullable|integer|min:0|max:100',
        ]);

        $toc->update($validated);

        return redirect()
            ->route('progress.index')
            ->with('success', 'Progress updated successfully.');
    }
}
