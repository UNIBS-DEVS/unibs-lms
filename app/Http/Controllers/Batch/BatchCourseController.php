<?php

namespace App\Http\Controllers\Batch;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Course;
use Illuminate\Http\Request;

class BatchCourseController extends Controller
{
    public function index()
    {
        $batches = Batch::with('customer') // eager load customer
            ->withCount('courses') //  add this to get courses_count
            ->orderBy('name')
            ->get();

        return view('batch_courses.index', compact('batches'));
    }

    public function show($id)
    {
        // Load batch with courses and customer
        $batch = Batch::with(['courses', 'customer'])->findOrFail($id);

        return view('batch_courses.show', compact('batch'));
    }

    public function edit($id)
    {
        $batch = Batch::with('courses')->findOrFail($id);

        // Get all active courses
        $allCourses = Course::where('status', 'active')
            ->orderBy('name')
            ->get();

        // Assigned courses (already in batch)
        $assignedCourses = $batch->courses;

        // Courses NOT in this batch
        $availableCourses = $allCourses->whereNotIn(
            'id',
            $assignedCourses->pluck('id')
        );

        return view('batch_courses.edit', compact('batch', 'availableCourses', 'assignedCourses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
        ]);

        $batch = Batch::findOrFail($id);

        $batch->courses()->sync($request->courses ?? []);

        return redirect()
            ->route('batch-courses.index')
            ->with('success', 'Courses updated successfully.');
    }
}
