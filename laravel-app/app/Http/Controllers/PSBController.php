<?php

namespace App\Http\Controllers;

use App\Models\PsbRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PSBController extends Controller
{
    /**
     * Public registration form.
     */
    public function register(Request $request)
    {
        $refCode = $request->query('ref');
        $referrer = null;

        if ($refCode) {
            $user = User::where('referral_code', strtoupper($refCode))->first();
            if ($user) {
                $referrer = [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }
        }

        return Inertia::render('PSB/Register', [
            'referrer' => $referrer
        ]);
    }

    /**
     * Store public registration submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_calon' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'nama_wali' => 'required|string|max:255',
            'no_hp_wali' => 'required|string|max:20',
            'berkas_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'berkas_akta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'berkas_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'referred_by' => 'nullable|exists:users,id',
        ]);

        $data = $validated;
        $data['token'] = \Illuminate\Support\Str::uuid()->toString();

        // Handle file uploads and cleanup old files if updating
        $existingRegistration = PsbRegistration::where('nama_calon', $validated['nama_calon'])
                                               ->where('no_hp_wali', $validated['no_hp_wali'])
                                               ->first();

        // If it's an update, don't overwrite the existing token
        if ($existingRegistration && $existingRegistration->token) {
            $data['token'] = $existingRegistration->token;
        }

        foreach (['berkas_kk', 'berkas_akta', 'berkas_ijazah'] as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if it exists
                if ($existingRegistration && $existingRegistration->$field) {
                    Storage::disk('public')->delete($existingRegistration->$field);
                }
                $data[$field] = $request->file($field)->store('psb_documents', 'public');
            }
        }

        PsbRegistration::updateOrCreate(
            [
                'nama_calon' => $validated['nama_calon'],
                'no_hp_wali' => $validated['no_hp_wali']
            ],
            $data
        );

        return redirect()->route('psb.success')->with('success', 'Pendaftaran berhasil dikirim!');
    }

    /**
     * Show the dynamic Stage 2 complete profile form.
     */
    public function editLengkap($token)
    {
        $registration = PsbRegistration::where('token', $token)->firstOrFail();
        $questions = \App\Models\FormQuestion::orderBy('order_num')->get();

        return Inertia::render('PSB/LengkapiData', [
            'registration' => $registration,
            'questions' => $questions
        ]);
    }

    /**
     * Handle submission of the dynamic Stage 2 form.
     */
    public function updateLengkap(Request $request, $token)
    {
        $registration = PsbRegistration::where('token', $token)->firstOrFail();
        $questions = \App\Models\FormQuestion::all();

        // Build dynamic validation rules
        $rules = [];
        foreach ($questions as $q) {
            $rule = $q->is_required ? ['required'] : ['nullable'];
            if ($q->type === 'file') {
                $rule[] = 'file';
                $rule[] = 'max:2048'; // 2MB max
            }
            $rules['question_' . $q->id] = $rule;
        }

        $validated = $request->validate($rules);

        foreach ($questions as $q) {
            $key = 'question_' . $q->id;
            
            // Check if there is already a response for this question to update/delete old file
            $existingResponse = $registration->responses()->where('form_question_id', $q->id)->first();

            if ($q->type === 'file') {
                if ($request->hasFile($key)) {
                    // Delete old file if exists
                    if ($existingResponse && $existingResponse->response_file_path) {
                        Storage::disk('public')->delete($existingResponse->response_file_path);
                    }
                    $path = $request->file($key)->store('psb_documents/tahap2', 'public');
                    $registration->responses()->updateOrCreate(
                        ['form_question_id' => $q->id],
                        ['response_file_path' => $path, 'response_text' => null]
                    );
                }
            } else {
                if (isset($validated[$key])) {
                    $val = is_array($validated[$key]) ? json_encode($validated[$key]) : $validated[$key];
                    $registration->responses()->updateOrCreate(
                        ['form_question_id' => $q->id],
                        ['response_text' => $val, 'response_file_path' => null]
                    );
                }
            }
        }

        return redirect()->route('psb.success')->with('success', 'Data lengkap berhasil disimpan!');
    }

    /**
     * Admin dashboard for registration management.
     */
    public function adminIndex()
    {
        $registrations = PsbRegistration::with(['referrer:id,name', 'responses.question'])->latest()->paginate(10);
        return Inertia::render('PSB/AdminIndex', [
            'registrations' => $registrations
        ]);
    }

    /**
     * Update selection status.
     */
    public function updateStatus(Request $request, PsbRegistration $psb)
    {
        $validated = $request->validate([
            'status_seleksi' => 'required|in:pending,diterima,cadangan,ditolak'
        ]);

        $psb->update($validated);

        return back()->with('success', 'Status pendaftaran berhasil diperbarui');
    }

    public function success()
    {
        return Inertia::render('PSB/Success');
    }
}
