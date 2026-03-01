<?php

namespace App\Http\Controllers;

use App\Models\ContentRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ContentRequestController extends Controller
{
    // Form submission for users
    public function create()
    {
        $user = Auth::user()->load('jabatan');
        return Inertia::render('Content/Create', [
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requester_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'format' => 'required|string|max:255',
            'deadline_date' => 'required|date',
            'deadline_time' => 'required',
            'size' => 'required|string|max:255',
            'output_format' => 'required|string|max:255',
            'include_logo' => 'boolean',
            'include_website' => 'boolean',
            'include_social_media' => 'boolean',
            'include_phone' => 'boolean',
            'content_description' => 'required|string',
            'special_notes' => 'nullable|string',
            'reference_link' => 'nullable|url',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['jabatan_id'] = Auth::user()->jabatan_id;
        $validated['status'] = 'Menunggu Assign';

        ContentRequest::create($validated);

        return redirect()->route('content-requests.my')->with('success', 'Pengajuan konten berhasil dikirim.');
    }

    // Dashboard for regular users to see their requests
    public function myRequests()
    {
        $requests = ContentRequest::where('user_id', Auth::id())
            ->with(['assignedTo'])
            ->latest()
            ->get();

        return Inertia::render('Content/MyRequests', [
            'requests' => $requests,
        ]);
    }
}
