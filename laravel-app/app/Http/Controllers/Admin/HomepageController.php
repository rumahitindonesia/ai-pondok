<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomepageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Homepage/Index', [
            'sections' => HomepageSection::all()
        ]);
    }

    public function update(Request $request, HomepageSection $section)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|array',
            'is_active' => 'required|boolean',
        ]);

        $section->update($validated);

        return back()->with('success', 'Section updated successfully.');
    }
}
