<?php

namespace App\Http\Controllers\MediaStaff;

use App\Http\Controllers\Controller;
use App\Models\ContentRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ContentTaskController extends Controller
{
    // Dashboard for Media Staff to see their assigned tasks
    public function index()
    {
        $tasks = ContentRequest::where('assigned_to', Auth::id())
            ->with(['requester', 'jabatan', 'assignedBy'])
            ->latest()
            ->get();

        return Inertia::render('Media/StaffDashboard', [
            'tasks' => $tasks,
        ]);
    }

    // Staff updating the status of their task
    public function updateStatus(Request $request, ContentRequest $contentTask)
    {
        // Ensure the staff is actually assigned to this task
        if ($contentTask->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:Ditugaskan,Proses,Revisi,Selesai',
        ]);

        $contentTask->update($validated);

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }
}
