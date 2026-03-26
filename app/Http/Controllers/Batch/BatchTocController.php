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
    // public function index(Batch $batch)
    // {
    //     $tocs = $batch->tocs()->orderBy('plan_start_date')->get();
    //     return view('batch_toc.index', compact('batch', 'tocs'));
    // }


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

        $tocs = $batch->tocs()->orderBy('plan_start_date')->get();
        return view('batch_toc.index', compact('batch', 'tocs'));
    }


    public function create(Batch $batch)
    {
        $courses = Course::all();

        $trainers = $batch->trainers; // batch assigned trainers

        return view('batch_toc.create', compact('batch', 'courses', 'trainers'));
    }

    public function store(Request $request, Batch $batch)
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
            'plan_start_date'  => 'required|date',
            'plan_end_date'    => 'required|date|after_or_equal:plan_start_date',
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
        $courses = Course::all();

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
            'plan_start_date'  => 'required|date',
            'plan_end_date'    => 'required|date|after_or_equal:plan_start_date',
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

    // Progress list page
    // public function progressIndex()
    // {
    //     if (Auth::user()->role === 'admin') {
    //         // Admin sees all batches with TOCs
    //         $batches = Batch::with(['tocs', 'trainers'])->get();
    //     }

    //     if (Auth::user()->role === 'trainer') {
    //         // Trainer sees only their assigned batches
    //         $batches = Auth::user()
    //             ->trainerBatches()
    //             ->with('tocs')
    //             ->get();
    //     }

    //     return view('batch_toc.progress-index', compact('batches'));
    // }

    public function progressIndex()
    {
        $user = auth()->user();

        if ($user->role === 'learner') {
            $batches = \App\Models\Batch::with('tocs')
                ->whereHas('learners', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->get();
        } else {
            $batches = \App\Models\Batch::with('tocs')->get();
        }

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
