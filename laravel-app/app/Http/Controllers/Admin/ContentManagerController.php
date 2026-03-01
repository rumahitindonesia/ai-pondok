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

        // Get users with media roles or Super Admin
        $staffMembers = User::role(['Media Manager', 'Media Staff', 'Super Admin'])->get();

        return Inertia::render('Admin/Content/ManagerDashboard', [
            'requests' => $requests,
            'staffMembers' => $staffMembers,
        ]);
    }

    // Assign a request to a media staff
    public function assign(Request $request, ContentRequest $contentRequest)
    {
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $status = $validated['assigned_to'] ? 'Ditugaskan' : 'Menunggu Assign';

        $contentRequest->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => Auth::id(),
            'status' => $status,
        ]);

        return back()->with('success', 'Penugasan berhasil diperbarui.');
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
