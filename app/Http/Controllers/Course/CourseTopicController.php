<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseTopic;
use Illuminate\Http\Request;

class CourseTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($courseId)
    {
        $course = Course::findOrFail($courseId);
        $topics = $course->topics; // We’ll define this relation
        return view('course_topics.index', compact('course', 'topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($courseId)
    {
        $course = Course::findOrFail($courseId);
        return view('course_topics.create', compact('course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remark' => 'nullable|string|max:255',
        ]);

        $course = Course::findOrFail($courseId);
        $course->topics()->create($request->only('title', 'description', 'remark'));

        return redirect()->route('courses.topics.index', $courseId)
            ->with('success', 'Topic added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($courseId, $id)
    {
        $course = Course::findOrFail($courseId);
        $topic = CourseTopic::findOrFail($id);
        return view('course_topics.edit', compact('course', 'topic'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $courseId, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remark' => 'nullable|string|max:255',
        ]);

        $topic = CourseTopic::findOrFail($id);
        $topic->update($request->only('title', 'description', 'remark'));

        return redirect()->route('courses.topics.index', $courseId)
            ->with('success', 'Topic updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($courseId, $id)
    {
        $topic = CourseTopic::findOrFail($id);
        $topic->delete();

        return redirect()->route('courses.topics.index', $courseId)
            ->with('success', 'Topic deleted successfully.');
    }

    // AJAX
    public function list(Course $course)
    {
        return $course->topics()->select('id', 'title')->get();
    }
}
