<?php

namespace App\Modules\Simaksi\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\POS\Models\PosOrder;
use App\Modules\Simaksi\Models\SimaksiBooking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicBookingCheckController extends Controller
{
    public function index(Request $request): View
    {
        $code = trim((string) $request->input('code', ''));
        $phone = trim((string) $request->input('phone', ''));

        $booking = null;
        $order = null;
        $hasSearched = !empty($code) || !empty($phone);
        $notFound = false;

        if ($hasSearched) {
            // 1. Search SIMAKSI Bookings
            $simaksiQuery = SimaksiBooking::with(['members', 'checkpoints']);

            if (!empty($code)) {
                $simaksiQuery->where(function ($q) use ($code) {
                    $q->where('booking_code', 'LIKE', "%{$code}%")
                      ->orWhere('qr_token', $code);
                });
            }

            if (!empty($phone)) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                $simaksiQuery->where(function ($q) use ($phone, $cleanPhone) {
                    $q->where('leader_phone', 'LIKE', "%{$phone}%")
                      ->orWhere('leader_phone', 'LIKE', "%{$cleanPhone}%");
                });
            }

            $booking = $simaksiQuery->latest()->first();

            // 2. Search Store / POS Preorders
            $orderQuery = PosOrder::with(['items.product', 'booking']);

            if (!empty($code)) {
                $orderQuery->where(function ($q) use ($code) {
                    $q->where('invoice_number', 'LIKE', "%{$code}%")
                      ->orWhereHas('booking', function ($b) use ($code) {
                          $b->where('booking_code', 'LIKE', "%{$code}%");
                      });
                });
            }

            if (!empty($phone)) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                $orderQuery->where(function ($q) use ($phone, $cleanPhone) {
                    $q->where('customer_phone', 'LIKE', "%{$phone}%")
                      ->orWhere('customer_phone', 'LIKE', "%{$cleanPhone}%");
                });
            }

            $order = $orderQuery->latest()->first();

            if (!$booking && !$order) {
                $notFound = true;
            }
        }

        return view('simaksi.check', compact(
            'code',
            'phone',
            'booking',
            'order',
            'hasSearched',
            'notFound'
        ));
    }
}
