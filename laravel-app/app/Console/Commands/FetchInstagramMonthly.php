<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SocialAccount;
use App\Models\ContentRequest;
use App\Models\User;
use App\Services\InstagramInsightsService;
use Carbon\Carbon;

class FetchInstagramMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ig:fetch-monthly {month} {year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Instagram posts for a given month and year and insert them into content requests';

    /**
     * Execute the console command.
     */
    public function handle(InstagramInsightsService $service)
    {
        $month = $this->argument('month');
        $year = $this->argument('year');

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $this->info("Fetching Instagram posts between {$startDate->toDateString()} and {$endDate->toDateString()}...");

        $accounts = SocialAccount::where('platform', 'instagram')->get();

        if ($accounts->isEmpty()) {
            $this->error("No Instagram accounts found.");
            return;
        }

        $adminUserId = User::whereHas('roles', function($q) { $q->where('name', 'Admin'); })->first()->id ?? 1;

        $postsInserted = 0;

        foreach ($accounts as $account) {
            $this->info("Processing account: {$account->instagram_username}");

            $mediaList = $service->getMediaByDateRange(
                $account->instagram_business_id,
                $startDate,
                $endDate,
                $account->access_token
            );

            $this->info("Found " . count($mediaList) . " posts for this month.");

            foreach ($mediaList as $media) {
                // Check if already exists
                if (ContentRequest::where('ig_media_id', $media['id'])->exists()) {
                    $this->line("Skipping {$media['id']}, already exists.");
                    continue;
                }

                $format = 'IMAGE'; // Default
                if ($media['media_type'] === 'VIDEO') {
                    $format = 'Mp4';
                } elseif ($media['media_type'] === 'IMAGE' || $media['media_type'] === 'CAROUSEL_ALBUM') {
                    $format = 'Jpg';
                }

                // Prepare ContentRequest
                $request = ContentRequest::create([
                    'user_id' => $adminUserId,
                    'requester_name' => 'Auto Sync',
                    'phone_number' => '00000',
                    'format' => 'Auto',
                    'deadline_date' => Carbon::parse($media['timestamp'])->toDateString(),
                    'deadline_time' => Carbon::parse($media['timestamp'])->toTimeString(),
                    'size' => 'Auto',
                    'output_format' => $format,
                    'content_description' => $media['caption'] ?? 'No caption',
                    'status' => 'Selesai',
                    'assigned_to' => $adminUserId,
                    'assigned_by' => $adminUserId,
                    'published_at' => Carbon::parse($media['timestamp']),
                    'published_url' => $media['permalink'] ?? null,
                    'ig_media_id' => $media['id'],
                ]);

                $this->info("Created ContentRequest #{$request->id} for media {$media['id']}");
                $postsInserted++;

                // Sync insights
                $insights = $service->getMediaInsights($media['id'], $account->access_token);
                if ($insights) {
                    $request->update([
                        'reach_count' => $insights['reach'] ?? 0,
                        'impressions_count' => $insights['impressions'] ?? 0,
                        'engagement_count' => $insights['engagement'] ?? 0,
                        'saved_count' => $insights['saved'] ?? 0,
                        'video_views_count' => $insights['video_views'] ?? 0,
                    ]);
                    $this->line("Synced insights for {$media['id']}");
                }
            }
        }

        $this->info("Done! Inserted {$postsInserted} posts.");
    }
}
