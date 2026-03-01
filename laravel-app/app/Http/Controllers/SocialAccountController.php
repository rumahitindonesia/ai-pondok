<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Services\InstagramInsightsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SocialAccountController extends Controller
{
    protected $instagramService;

    public function __construct(InstagramInsightsService $instagramService)
    {
        $this->instagramService = $instagramService;
    }

    public function redirectToInstagram()
    {
        return redirect()->away($this->instagramService->getLoginUrl());
    }

    public function handleInstagramCallback(Request $request)
    {
        $code = $request->query('code');
        if (!$code) {
            return redirect()->route('admin.content.manager.index')->with('error', 'Authentication failed');
        }

        $shortLivedToken = $this->instagramService->getAccessTokenFromCode($code);
        if (!$shortLivedToken) {
            return redirect()->route('admin.content.manager.index')->with('error', 'Failed to get access token');
        }

        $longLivedData = $this->instagramService->getLongLivedToken($shortLivedToken);
        if (!$longLivedData) {
            return redirect()->route('admin.content.manager.index')->with('error', 'Failed to get long-lived token');
        }

        $accounts = $this->instagramService->getInstagramAccounts($longLivedData['access_token']);
        
        if (empty($accounts)) {
            return redirect()->route('admin.content.manager.index')->with('error', 'No Instagram Business accounts found linked to your Facebook pages');
        }

        // For now, we take the first account found.
        $accountData = $accounts[0];

        SocialAccount::updateOrCreate(
            ['user_id' => Auth::id(), 'platform' => 'instagram'],
            [
                'platform_user_id' => $accountData['instagram_business_id'],
                'access_token' => $longLivedData['access_token'],
                'token_expires_at' => now()->addSeconds($longLivedData['expires_in'] ?? 5184000), // Default 60 days
                'instagram_username' => $accountData['instagram_username'],
                'instagram_business_id' => $accountData['instagram_business_id'],
                'facebook_page_id' => $accountData['page_id'],
            ]
        );

        return redirect()->route('admin.content.manager.index')->with('success', 'Instagram successfully connected!');
    }

    public function disconnect()
    {
        SocialAccount::where('user_id', Auth::id())->where('platform', 'instagram')->delete();
        return back()->with('success', 'Koneksi sosial media berhasil dihapus.');
    }

    public function syncMediaInsights(Request $request, \App\Models\ContentRequest $contentRequest)
    {
        $account = SocialAccount::where('user_id', Auth::id())->where('platform', 'instagram')->first();
        if (!$account) {
            return back()->with('error', 'Silakan hubungkan akun Instagram terlebih dahulu.');
        }

        if (!$contentRequest->published_url && !$contentRequest->ig_media_id) {
            return back()->with('error', 'Link publikasi (IG URL) belum diisi.');
        }

        try {
            // Find Media ID by URL if not set
            if (!$contentRequest->ig_media_id) {
                $mediaId = $this->instagramService->findMediaIdByUrl(
                    $account->instagram_business_id,
                    $contentRequest->published_url,
                    $account->access_token
                );

                if ($mediaId) {
                    $contentRequest->update(['ig_media_id' => $mediaId]);
                } else {
                    return back()->with('error', 'Gagal menemukan ID Media Instagram dari URL tersebut. Pastikan konten sudah ter-publish di akun yang terhubung.');
                }
            }

            // Sync insights
            $insights = $this->instagramService->getMediaInsights($contentRequest->ig_media_id, $account->access_token);
            
            if ($insights) {
                $contentRequest->update([
                    'reach_count' => $insights['reach'] ?? $contentRequest->reach_count,
                    'impressions_count' => $insights['impressions'] ?? $contentRequest->impressions_count,
                    'engagement_count' => $insights['engagement'] ?? $contentRequest->engagement_count,
                    'saved_count' => $insights['saved'] ?? $contentRequest->saved_count,
                    'video_views_count' => $insights['video_views'] ?? $contentRequest->video_views_count,
                ]);

                return back()->with('success', 'Wush! Data insight Instagram berhasil diperbarui secara otomatis.');
            }

            return back()->with('error', 'Gagal mengambil data insight dari Instagram.');
        } catch (\Exception $e) {
            Log::error('Instagram sync error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat sync: ' . $e->getMessage());
        }
    }
}
