<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';
    protected $model = 'gemini-flash-latest';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.model', 'gemini-flash-latest');
    }

    public function generateStrategicAdvice($stats, $topContent)
    {
        if (!$this->apiKey) {
            return "Gemini API Key matches not configured. Please add GEMINI_API_KEY to your .env file.";
        }

        $prompt = $this->buildPrompt($stats, $topContent);

        try {
            $response = Http::post("{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 4096,
                ]
            ]);

            if ($response->failed()) {
                Log::error("Gemini API Error", $response->json());
                return "Maaf, sistem AI sedang beristirahat sebentar. Silakan coba lagi nanti.";
            }

            return $response->json('candidates.0.content.parts.0.text') ?? "Insight tidak tersedia saat ini.";
        } catch (\Exception $e) {
            Log::error("Gemini Service Exception: " . $e->getMessage());
            return "Terjadi kesalahan saat menghubungi AI.";
        }
    }

    protected function buildPrompt($stats, $topContent)
    {
        $statsJson = json_encode($stats, JSON_PRETTY_PRINT);
        $contentList = $topContent->map(function($c) {
            return "- [{$c->output_format}] Description: {$c->content_description} (Reach: {$c->reach_count}, Engage: {$c->engagement_count})";
        })->implode("\n");

        return "
Anda adalah seorang Media Strategic Consultant Expert untuk 'Rumah IT Indonesia'. 
Tugas Anda adalah menganalisis data performa konten Instagram berikut dan memberikan saran taktis yang WOW dan aplikatif.

DATA STATISTIK:
{$statsJson}

KONTEN TERBAIK SAAT INI:
{$contentList}

INSTRUKSI:
1. Berikan analisis singkat kenapa konten terbaik tersebut bisa berhasil.
2. Bandingkan performa antar format (Reels vs Carousel/Post) berdasarkan data.
3. Berikan 3 poin saran konkret untuk strategi konten minggu depan.
4. Gunakan gaya bahasa yang profesional, visioner, namun tetap santai (ala agensi kreatif).
5. Jawaban harus dalam bahasa Indonesia yang baik.
6. Hindari jawaban yang terlalu umum, berikan sentuhan 'Rumah IT' (fokus ke teknologi/edukasi/IT).
7. Gunakan format Markdown yang cantik.
";
    }
}
