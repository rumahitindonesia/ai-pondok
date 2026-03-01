<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentRequest;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ContentAnalyticsController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index()
    {
        // 1. Overall Aggregates (Selesai posts only)
        $overallStats = ContentRequest::where('status', 'Selesai')
            ->select([
                DB::raw('SUM(reach_count) as total_reach'),
                DB::raw('SUM(engagement_count) as total_engagement'),
                DB::raw('SUM(impressions_count) as total_impressions'),
                DB::raw('SUM(saved_count) as total_saved'),
                DB::raw('SUM(video_views_count) as total_views'),
                DB::raw('COUNT(*) as total_posts')
            ])
            ->first();

        // 2. Performance by Format (Reels vs Static/Carousel)
        // Note: Mapping 'MP4 (Video)' usually corresponds to Reels if it has video_views
        $formatStats = ContentRequest::where('status', 'Selesai')
            ->select([
                'output_format',
                DB::raw('AVG(reach_count) as avg_reach'),
                DB::raw('AVG(engagement_count) as avg_engagement'),
                DB::raw('AVG(saved_count) as avg_saved'),
                DB::raw('COUNT(*) as count')
            ])
            ->groupBy('output_format')
            ->get();

        // 3. Top 5 Best Performing Content (by Reach)
        $topContent = ContentRequest::where('status', 'Selesai')
            ->with(['requester', 'assignedTo'])
            ->orderByDesc('reach_count')
            ->limit(5)
            ->get();

        // 4. Monthly Trend (last 6 months)
        $monthlyTrend = ContentRequest::where('status', 'Selesai')
            ->where('published_at', '>=', now()->subMonths(6))
            ->select([
                DB::raw('DATE_FORMAT(published_at, "%M %Y") as month'),
                DB::raw('SUM(reach_count) as reach'),
                DB::raw('COUNT(*) as post_count')
            ])
            ->groupBy('month')
            ->orderBy('published_at')
            ->get();

        // 5. AI Strategic Advice
        $aiInsight = "Data tidak mencukupi untuk analisis AI saat ini.";
        if ($overallStats->total_posts > 0) {
            $aiInsight = $this->gemini->generateStrategicAdvice($formatStats->toArray(), $topContent);
        }

        return Inertia::render('Admin/Content/ContentAnalytics', [
            'overallStats' => $overallStats,
            'formatStats' => $formatStats,
            'topContent' => $topContent,
            'monthlyTrend' => $monthlyTrend,
            'aiInsight' => $aiInsight,
        ]);
    }
}
