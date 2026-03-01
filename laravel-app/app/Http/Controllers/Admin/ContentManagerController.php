<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ContentManagerController extends Controller
{
    // Dashboard for Media Manager to see all requests
    public function index()
    {
        $requests = ContentRequest::with(['requester', 'jabatan', 'assignedTo', 'assignedBy'])
            ->latest()
            ->get();

        // Get all media staff (for assignment dropdown)
        // Ideally filter by role/permission, but here getting users who could be staff
        // You might need to adjust the query based on your actual role structure
        $staffMembers = User::all();

        return Inertia::render('Admin/Content/ManagerDashboard', [
            'requests' => $requests,
            'staffMembers' => $staffMembers,
        ]);
    }

    // Assign a request to a media staff
    public function assign(Request $request, ContentRequest $contentRequest)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $contentRequest->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => Auth::id(),
            'status' => 'Ditugaskan',
        ]);

        return back()->with('success', 'Tugas berhasil di-assign ke staf.');
    }

    // Manager can force update status
    public function updateStatus(Request $request, ContentRequest $contentRequest)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Menunggu Assign,Ditugaskan,Proses,Revisi,Selesai,Batal',
        ]);

        $contentRequest->update($validated);

        return back()->with('success', 'Status berhasil diperbarui.');
    }
}
