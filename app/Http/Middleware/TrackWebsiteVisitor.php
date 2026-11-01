<?php

namespace App\Http\Middleware;

use App\Modules\Admin\Models\WebsiteVisit;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackWebsiteVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya catat request GET sukses dari pengunjung publik
        if (! $request->isMethod('GET') || $response->getStatusCode() >= 400) {
            return $response;
        }

        $path = $request->path();

        // Kecualikan rute internal, admin, pos, fieldops, aset statis, atau API
        if ($request->is('admin*', 'pos*', 'fieldops*', 'rental/*', 'consignment*', 'api*', 'storage*', 'assets*', 'build*')) {
            return $response;
        }

        // Tentukan tipe halaman
        $pageType = 'landing';
        if ($request->is('simaksi*')) {
            $pageType = 'booking';
        } elseif ($request->is('peta*', 'peta-jalur*')) {
            $pageType = 'trail_map';
        } elseif ($request->is('sewa*')) {
            $pageType = 'rental';
        } elseif ($request->is('store*')) {
            $pageType = 'store';
        } elseif ($request->is('cek-booking*')) {
            $pageType = 'ticket_check';
        }

        try {
            $sessionId = $request->session()->getId() ?: ('anon_' . substr(md5($request->ip() . $request->userAgent()), 0, 16));
            $today = today()->toDateString();
            $cacheKey = "visit_logged_{$sessionId}_{$pageType}_{$today}";

            // Debounce: hanya catat 1 kali per tipe halaman per session per hari
            if (! session()->has($cacheKey)) {
                WebsiteVisit::create([
                    'session_id' => $sessionId,
                    'ip_hash' => hash('sha256', $request->ip() ?? '127.0.0.1'),
                    'page_path' => '/' . ltrim($path, '/'),
                    'page_type' => $pageType,
                    'visited_date' => $today,
                ]);

                session()->put($cacheKey, true);
            }
        } catch (Exception $e) {
            // Jangan gagalkan request jika logging visitor gagal
        }

        return $response;
    }
}
