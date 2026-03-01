<?php

namespace App\Services;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InstagramInsightsService
{
    protected $baseUrl = 'https://graph.facebook.com/v19.0';
    protected $appId;
    protected $appSecret;
    protected $redirectUri;

    public function __construct()
    {
        $this->appId = config('services.meta.app_id');
        $this->appSecret = config('services.meta.app_secret');
        $this->redirectUri = config('services.meta.redirect_uri');
    }

    public function getLoginUrl()
    {
        $scopes = [
            'instagram_basic',
            'instagram_manage_insights',
            'pages_read_engagement',
        ];

        return "https://www.facebook.com/v19.0/dialog/oauth?" . http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(',', $scopes),
            'response_type' => 'code',
        ]);
    }

    public function getAccessTokenFromCode($code)
    {
        $response = Http::get("{$this->baseUrl}/oauth/access_token", [
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'client_secret' => $this->appSecret,
            'code' => $code,
        ]);

        if ($response->failed()) {
            Log::error('Instagram OAuth failed', $response->json());
            return null;
        }

        return $response->json('access_token');
    }

    public function getLongLivedToken($shortLivedToken)
    {
        $response = Http::get("{$this->baseUrl}/oauth/access_token", [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'fb_exchange_token' => $shortLivedToken,
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    public function getInstagramAccounts($accessToken)
    {
        // 1. Get Pages
        $pagesResponse = Http::get("{$this->baseUrl}/me/accounts", [
            'access_token' => $accessToken,
        ]);

        if ($pagesResponse->failed()) {
            return [];
        }

        $pages = $pagesResponse->json('data');
        $accounts = [];

        foreach ($pages as $page) {
            // 2. Get Instagram Business Account for each page
            $igResponse = Http::get("{$this->baseUrl}/{$page['id']}", [
                'fields' => 'instagram_business_account{id,username}',
                'access_token' => $accessToken,
            ]);

            if ($igResponse->successful() && $igResponse->json('instagram_business_account')) {
                $igAccount = $igResponse->json('instagram_business_account');
                $accounts[] = [
                    'page_id' => $page['id'],
                    'page_name' => $page['name'],
                    'instagram_business_id' => $igAccount['id'],
                    'instagram_username' => $igAccount['username'],
                ];
            }
        }

        return $accounts;
    }

    public function getMediaInsights($igMediaId, $accessToken)
    {
        // 1. Get media type/product to determine correct metrics
        $mediaInfo = Http::get("{$this->baseUrl}/{$igMediaId}", [
            'fields' => 'media_product_type,media_type',
            'access_token' => $accessToken,
        ]);

        $productType = $mediaInfo->json('media_product_type'); // REELS, FEED, AD, etc.
        
        if ($productType === 'REELS') {
            $metrics = 'views,reach,saved,total_interactions';
            $map = [
                'views' => 'video_views',
                'reach' => 'reach',
                'saved' => 'saved',
                'total_interactions' => 'engagement'
            ];
        } else {
            $metrics = 'impressions,reach,engagement,saved';
            $map = [
                'impressions' => 'impressions',
                'reach' => 'reach',
                'engagement' => 'engagement',
                'saved' => 'saved'
            ];
            // Add video_views for non-reel videos
            if ($mediaInfo->json('media_type') === 'VIDEO') {
                $metrics .= ',video_views';
                $map['video_views'] = 'video_views';
            }
        }
        
        $response = Http::get("{$this->baseUrl}/{$igMediaId}/insights", [
            'metric' => $metrics,
            'access_token' => $accessToken,
        ]);

        if ($response->failed()) {
            Log::error("Failed to fetch insights for media {$igMediaId} ({$productType})", $response->json());
            return null;
        }

        $data = $response->json('data');
        $insights = [];
        foreach ($data as $metric) {
            $internalKey = $map[$metric['name']] ?? $metric['name'];
            $insights[$internalKey] = $metric['values'][0]['value'] ?? 0;
        }

        return $insights;
    }

    public function findMediaIdByUrl($igBusinessId, $url, $accessToken)
    {
        // Support /p/, /reel/, and /reels/
        preg_match('/\/(?:p|reel|reels)\/([^\/?#]+)/', $url, $matches);
        
        $shortcode = $matches[1] ?? null;
        if (!$shortcode) return null;

        // Search in user's media
        $response = Http::get("{$this->baseUrl}/{$igBusinessId}/media", [
            'fields' => 'id,shortcode',
            'access_token' => $accessToken,
        ]);

        if ($response->failed()) return null;

        foreach ($response->json('data') as $media) {
            if ($media['shortcode'] === $shortcode) {
                return $media['id'];
            }
        }

        return null;
    }

    public function getMediaByDateRange($igBusinessId, $startDate, $endDate, $accessToken)
    {
        $allMedia = [];
        $url = "{$this->baseUrl}/{$igBusinessId}/media";
        $params = [
            'fields' => 'id,caption,media_type,media_product_type,media_url,permalink,timestamp,shortcode',
            'access_token' => $accessToken,
            'limit' => 50,
        ];

        while ($url) {
            $response = Http::get($url, $params);

            if ($response->failed()) {
                Log::error("Failed to fetch media for IG Business ID {$igBusinessId}", $response->json());
                break;
            }

            $data = $response->json();
            
            foreach ($data['data'] as $media) {
                $postDate = Carbon::parse($media['timestamp']);
                if ($postDate->between($startDate, $endDate)) {
                    $allMedia[] = $media;
                } elseif ($postDate->lt($startDate)) {
                    // Stop paginating if we go older than the start date
                    $url = null;
                }
            }

            if (!$url) break;

            $url = $data['paging']['next'] ?? null;
            $params = [];
        }

        return $allMedia;
    }
}
