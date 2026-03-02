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

        // Get all users so manager can assign to anyone for now
        $staffMembers = User::orderBy('name')->get();
        
        $instagramAccount = \App\Models\SocialAccount::where('user_id', Auth::id())
            ->where('platform', 'instagram')
            ->first();

        return Inertia::render('Admin/Content/ManagerDashboard', [
            'requests' => $requests,
            'staffMembers' => $staffMembers,
            'instagramAccount' => $instagramAccount,
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

    // Update publishing info and metrics
    public function updateMetrics(Request $request, ContentRequest $contentRequest)
    {
        $validated = $request->validate([
            'published_at' => 'nullable|date',
            'published_url' => 'nullable|url',
            'reach_count' => 'nullable|integer|min:0',
            'engagement_count' => 'nullable|integer|min:0',
            'link_clicks' => 'nullable|integer|min:0',
            'insight_notes' => 'nullable|string',
            'status' => 'nullable|string|in:Menunggu Assign,Ditugaskan,Proses,Revisi,Selesai,Batal',
        ]);

        $contentRequest->update($validated);

        return back()->with('success', 'Metrik performa konten berhasil diperbarui.');
    }
}
