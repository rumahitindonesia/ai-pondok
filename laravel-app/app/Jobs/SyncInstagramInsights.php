<?php

namespace App\Jobs;

use App\Models\ContentRequest;
use App\Models\SocialAccount;
use App\Services\InstagramInsightsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncInstagramInsights implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(InstagramInsightsService $service): void
    {
        Log::info('Starting scheduled Instagram insights sync...');

        $accounts = SocialAccount::where('platform', 'instagram')->get();

        foreach ($accounts as $account) {
            $requests = ContentRequest::where(function($query) {
                    $query->whereNotNull('ig_media_id')
                          ->orWhereNotNull('published_url');
                })
                ->where('status', 'Selesai')
                ->where('created_at', '>', now()->subDays(30)) // Only sync last 30 days of content
                ->get();

            foreach ($requests as $request) {
                try {
                    // Try to find Media ID if not exists
                    if (!$request->ig_media_id && $request->published_url) {
                        $mediaId = $service->findMediaIdByUrl(
                            $account->instagram_business_id,
                            $request->published_url,
                            $account->access_token
                        );
                        if ($mediaId) {
                            $request->update(['ig_media_id' => $mediaId]);
                        }
                    }

                    if ($request->ig_media_id) {
                        $insights = $service->getMediaInsights($request->ig_media_id, $account->access_token);
                        if ($insights) {
                            $request->update([
                                'reach_count' => $insights['reach'] ?? $request->reach_count,
                                'impressions_count' => $insights['impressions'] ?? $request->impressions_count,
                                'engagement_count' => $insights['engagement'] ?? $request->engagement_count,
                                'saved_count' => $insights['saved'] ?? $request->saved_count,
                                'video_views_count' => $insights['video_views'] ?? $request->video_views_count,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to sync insights for request #{$request->id}: " . $e->getMessage());
                }
            }
        }

        Log::info('Instagram insights sync completed.');
    }
}
