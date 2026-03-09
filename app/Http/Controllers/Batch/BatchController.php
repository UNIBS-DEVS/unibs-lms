<?php

namespace App\Http\Controllers\Batch;

use App\Http\Controllers\Controller;
use App\Mail\BatchCreatedMail;
use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with(['courses', 'trainers', 'customer'])
            ->latest()
            ->get();

        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        $customers = User::where('role', 'customer')->where('status', 'active')->get();

        return view('batches.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $limitByPresentValue = function ($attribute, $value, $fail) use ($request) {
            if ($request->filled('present_value') && $value > $request->present_value) {
                $label = ucfirst(str_replace('_', ' ', $attribute));
                $fail("{$label} points cannot be greater than Present points.");
            }
        };

        $validated = $request->validate([
            'name'        => 'required|unique:batches,name|max:255',
            'status'      => 'required|in:active,inactive',
            'customer_id' => 'required|exists:users,id',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',

            // ADD THESE
            'attendance_percentage' => 'nullable|integer|min:0|max:100',
            'quiz_percentage'       => 'nullable|integer|min:0|max:100',
            'feedback_percentage'   => 'nullable|integer|min:0|max:100',

            'red_percentage'   => 'nullable|integer|min:0|max:100',
            'amber_percentage' => 'nullable|integer|min:0|max:100',
            'green_percentage' => 'nullable|integer|min:0|max:100',

            'present_value' => 'nullable|integer|min:0',

            'late_entry_value' => ['nullable', 'numeric', 'min:0', $limitByPresentValue],
            'early_exit_value' => ['nullable',  'numeric', 'min:0', $limitByPresentValue],
        ]);

        // ✅ Enforce total = 100
        $total =
            ($validated['attendance_percentage'] ?? 0) +
            ($validated['quiz_percentage'] ?? 0) +
            ($validated['feedback_percentage'] ?? 0);

        if ($total !== 100) {
            return back()
                ->withErrors([
                    'attendance_percentage' =>
                    'Attendance %, Quiz %, and Feedback % must total exactly 100.',
                ])
                ->withInput();
        }

        $batch = Batch::create($validated);
        $batch->load('customer');

        $createdByName = Auth::user()->name;

        // ✅ Same as UserController
        $clientCode = session('client_code');

        if (!$clientCode) {
            return redirect()
                ->route('batches.index')
                ->with('error', 'Client session expired. Please login again.');
        }

        if ($batch->customer && $batch->customer->email) {
            Mail::to($batch->customer->email)->later(
                now()->addSeconds(10),
                new BatchCreatedMail(
                    $batch->id,
                    $createdByName,
                    $clientCode
                )
            );
        }
        return redirect()
            ->route('batches.index')
            ->with('success', 'Batch created successfully.');
    }

    public function show(Batch $batch)
    {
        // Load relations explicitly (safe + clear)
        $batch->load(['customer']);

        return view('batches.show', compact('batch'));
    }

    public function edit(Batch $batch)
    {
        // $trainers = User::where('role', 'trainer')->where('status', 'active')->get();
        $customers = User::where('role', 'customer')->where('status', 'active')->get();
        $courses = Course::where('status', 'active')->get();

        return view('batches.edit', compact('batch', 'customers'));
    }

    public function update(Request $request, Batch $batch)
    {
        $limitByPresentValue = function ($attribute, $value, $fail) use ($request) {
            if ($request->filled('present_value') && $value > $request->present_value) {
                $label = ucfirst(str_replace('_', ' ', $attribute));
                $fail("{$label} points cannot be greater than Present points.");
            }
        };

        $validated = $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('batches', 'name')->ignore($batch->id),
            ],

            'status'      => 'required|in:active,inactive',
            'customer_id' => 'required|exists:users,id',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',

            // 🔹 ADD THESE
            'attendance_percentage' => 'nullable|integer|min:0|max:100',
            'quiz_percentage'       => 'nullable|integer|min:0|max:100',
            'feedback_percentage'   => 'nullable|integer|min:0|max:100',

            'red_percentage'   => 'nullable|integer|min:0|max:100',
            'amber_percentage' => 'nullable|integer|min:0|max:100',
            'green_percentage' => 'nullable|integer|min:0|max:100',

            'present_value' => 'nullable|integer|min:0',

            'late_entry_value' => ['nullable', 'min:0', $limitByPresentValue],
            'early_exit_value' => ['nullable', 'min:0', $limitByPresentValue],
        ]);

        // ✅ Enforce total = 100
        $total =
            ($validated['attendance_percentage'] ?? 0) +
            ($validated['quiz_percentage'] ?? 0) +
            ($validated['feedback_percentage'] ?? 0);

        if ($total !== 100) {
            return back()
                ->withErrors([
                    'attendance_percentage' =>
                    'Attendance %, Quiz %, and Feedback % must total exactly 100.',
                ])
                ->withInput();
        }

        $batch->update($validated);

        return redirect()
            ->route('batches.index')
            ->with('success', 'Batch updated successfully.');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();

        return redirect()
            ->route('batches.index')
            ->with('success', 'Batch deleted successfully.');
    }
}
