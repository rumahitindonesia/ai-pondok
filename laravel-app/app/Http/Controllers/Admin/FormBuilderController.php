<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FormQuestion;
use Inertia\Inertia;

class FormBuilderController extends Controller
{
    public function index()
    {
        $questions = FormQuestion::orderBy('order_num')->get();
        return Inertia::render('Admin/PSB/FormBuilder', [
            'questions' => $questions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_text' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,radio,checkbox,file,date',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
        ]);

        $maxOrder = FormQuestion::max('order_num') ?? 0;
        $validated['order_num'] = $maxOrder + 1;

        if ($validated['type'] !== 'radio' && $validated['type'] !== 'checkbox') {
            $validated['options'] = null;
        }

        FormQuestion::create($validated);

        return back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function update(Request $request, FormQuestion $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,radio,checkbox,file,date',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
        ]);

        if ($validated['type'] !== 'radio' && $validated['type'] !== 'checkbox') {
            $validated['options'] = null;
        }

        $question->update($validated);

        return back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy(FormQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Pertanyaan berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'exists:form_questions,id'
        ]);

        foreach ($validated['ordered_ids'] as $index => $id) {
            FormQuestion::where('id', $id)->update(['order_num' => $index + 1]);
        }

        return back()->with('success', 'Urutan berhasil diperbarui.');
    }
}
