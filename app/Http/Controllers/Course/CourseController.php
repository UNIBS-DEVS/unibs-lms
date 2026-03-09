<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount(['topics', 'batches'])
            ->orderBy('name')
            ->get();

        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'study_material_path' => 'required|string|max:500',
            'category'   => 'required|in:technical,managerial,functional,soft skill,others',
            'status'     => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->route('activities.create')->withInput()->withErrors($validator);
        }

        Course::create([
            'name'       => $request->name,
            'study_material_path' => $request->study_material_path,
            'category'   => $request->category,
            'status'     => $request->status,
        ]);

        return redirect()
            ->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $course = Course::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'study_material_path' => 'required|string|max:500',
            'category'   => 'required|in:technical,managerial,functional,soft skill,others',
            'status'     => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('courses.edit', $course->id)
                ->withErrors($validator)
                ->withInput();
        }

        $course->update([
            'name'       => $request->name,
            'study_material_path' => $request->study_material_path,
            'category'   => $request->category,
            'status'     => $request->status,
        ]);

        return redirect()
            ->route('courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()
            ->route('courses.index')
            ->with('success', 'Delete Course successfully.');
    }
}
