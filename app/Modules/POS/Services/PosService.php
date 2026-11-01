<?php

namespace App\Modules\POS\Services;

use App\Modules\Consignment\Models\ConsignmentMitra;
use App\Modules\POS\Models\CashierShift;
use App\Modules\POS\Models\PosOrder;
use App\Modules\POS\Models\PosOrderItem;
use App\Modules\Rental\Services\RentalService;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Store\Models\Product;
use App\Modules\Store\Services\StoreService;
use App\Shared\Enums\PaymentMethod;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosService
{
    public function __construct(
        protected StoreService $storeService,
        protected RentalService $rentalService,
    ) {}

    public function getActiveShift(int $cashierUserId): ?CashierShift
    {
        return CashierShift::where('cashier_user_id', $cashierUserId)
            ->where('status', 'OPEN')
            ->latest()
            ->first();
    }

    public function openShift(int $cashierUserId, float $openingCash): CashierShift
    {
        $existing = $this->getActiveShift($cashierUserId);
        if ($existing) {
            return $existing;
        }

        $shiftNumber = CashierShift::where('cashier_user_id', $cashierUserId)
            ->whereDate('created_at', today())
            ->count() + 1;

        return CashierShift::create([
            'cashier_user_id' => $cashierUserId,
            'shift_number' => $shiftNumber,
            'opened_at' => now(),
            'opening_cash' => $openingCash,
            'status' => 'OPEN',
        ]);
    }

    public function closeShift(int $shiftId, float $physicalCash, ?string $notes = null): CashierShift
    {
        $shift = CashierShift::findOrFail($shiftId);

        if ($shift->status === 'CLOSED') {
            throw new Exception("Shift #{$shift->shift_number} sudah dalam status DITUTUP sebelumnya.");
        }

        $closedAt = now();

        // Calculate sales during this shift
        $cashSales = PosOrder::where('cashier_user_id', $shift->cashier_user_id)
            ->whereBetween('created_at', [$shift->opened_at, $closedAt])
            ->where('payment_method', PaymentMethod::CASH)
            ->sum('total_amount');

        $qrisSales = PosOrder::where('cashier_user_id', $shift->cashier_user_id)
            ->whereBetween('created_at', [$shift->opened_at, $closedAt])
            ->where('payment_method', PaymentMethod::QRIS)
            ->sum('total_amount');

        $transferSales = PosOrder::where('cashier_user_id', $shift->cashier_user_id)
            ->whereBetween('created_at', [$shift->opened_at, $closedAt])
            ->where('payment_method', PaymentMethod::TRANSFER)
            ->sum('total_amount');

        $expectedCash = $shift->opening_cash + $cashSales;
        $difference = $physicalCash - $expectedCash;

        $notesFormatted = trim(($notes ? $notes . ' | ' : '') . 'Transfer Bank: Rp ' . number_format($transferSales, 0, ',', '.'));

        $shift->update([
            'closed_at' => $closedAt,
            'closing_cash_physical' => $physicalCash,
            'closing_cash_system' => $expectedCash,
            'closing_qris_system' => $qrisSales,
            'cash_difference' => $difference,
            'notes' => $notesFormatted,
            'status' => 'CLOSED',
        ]);

        return $shift;
    }

    public function processCheckout(array $data, ?int $cashierUserId = null): PosOrder
    {
        return DB::transaction(function () use ($data, $cashierUserId) {
            $bookingId = $data['booking_id'] ?? null;
            $items = $data['items'] ?? [];
            $rentals = $data['rentals'] ?? [];
            $paymentMethod = PaymentMethod::from($data['payment_method'] ?? 'CASH');
            $isPreorderPickup = (bool) ($data['is_preorder_pickup'] ?? false);

            $subtotalSimaksi = 0;
            $subtotalStore = 0;
            $subtotalRental = 0;
            $subtotalOjek = 0;
            $ecoDeposit = 0;
            $rentalDeposit = 0;

            $invoiceNumber = 'INV-' . now()->format('ymd') . '-' . Str::upper(Str::random(4));

            $order = PosOrder::create([
                'invoice_number' => $invoiceNumber,
                'booking_id' => $bookingId,
                'cashier_user_id' => $cashierUserId,
                'customer_name' => $data['customer_name'] ?? 'Pendaki Umum',
                'customer_phone' => $data['customer_phone'] ?? null,
                'subtotal_simaksi' => 0,
                'subtotal_store' => 0,
                'subtotal_rental' => 0,
                'subtotal_ojek' => 0,
                'eco_deposit' => 0,
                'rental_deposit' => 0,
                'total_amount' => 0,
                'paid_amount' => (float) ($data['paid_amount'] ?? 0),
                'change_amount' => 0,
                'payment_method' => $paymentMethod,
                'payment_status' => 'PAID',
                'is_preorder_pickup' => $isPreorderPickup,
                'notes' => $data['notes'] ?? null,
            ]);

            $booking = null;
            // If there's a booking attached: only bill SIMAKSI & Eco-Deposit if not a pure store preorder
            if ($bookingId) {
                $booking = SimaksiBooking::findOrFail($bookingId);

                if ($booking->status === \App\Shared\Enums\BookingStatus::CANCELLED) {
                    throw new Exception("Booking {$booking->booking_code} sudah dibatalkan dan tidak dapat diproses di kasir.");
                }

                if ($booking->status === \App\Shared\Enums\BookingStatus::CHECKED_OUT) {
                    throw new Exception("Booking {$booking->booking_code} sudah selesai pendakian (checkout) dan tidak dapat diproses ulang di kasir.");
                }

                if (! $isPreorderPickup && $booking->status === \App\Shared\Enums\BookingStatus::PENDING_PAYMENT) {
                    $subtotalSimaksi = (float) $booking->total_ticket_amount;
                    $ecoDeposit = (float) $booking->eco_deposit_amount;

                    PosOrderItem::create([
                        'pos_order_id' => $order->id,
                        'item_type' => 'SIMAKSI',
                        'reference_id' => $booking->id,
                        'item_name' => "Tiket SIMAKSI Cikuray ({$booking->total_hikers} orang)",
                        'qty' => $booking->total_hikers,
                        'unit_price' => $booking->ticket_fee_per_person,
                        'subtotal' => $subtotalSimaksi,
                        'cost_price' => 0,
                    ]);

                    PosOrderItem::create([
                        'pos_order_id' => $order->id,
                        'item_type' => 'ECO_DEPOSIT',
                        'reference_id' => $booking->id,
                        'item_name' => 'Jaminan Sampah (Eco-Deposit)',
                        'qty' => 1,
                        'unit_price' => $ecoDeposit,
                        'subtotal' => $ecoDeposit,
                        'cost_price' => 0,
                    ]);

                    // Automatically mark booking confirmed & paid
                    $booking->update([
                        'status' => \App\Shared\Enums\BookingStatus::CONFIRMED,
                        'paid_at' => now(),
                    ]);
                }
            }

            // Process Store items
            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) ($item['qty'] ?? 1);
                $unitPrice = (float) $product->selling_price;
                $lineSubtotal = $unitPrice * $qty;
                $costPrice = (float) $product->cost_price;

                $this->storeService->adjustStock(
                    $product->id,
                    -$qty,
                    'OUT_SALE',
                    "Penjualan Kasir POS {$invoiceNumber}",
                    $cashierUserId
                );

                $itemType = $product->isConsignment() ? 'STORE_KONSINYASI' : 'STORE_BELI_PUTUS';

                PosOrderItem::create([
                    'pos_order_id' => $order->id,
                    'item_type' => $itemType,
                    'reference_id' => $product->id,
                    'item_name' => $product->name,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                    'cost_price' => $costPrice,
                    'mitra_id' => $product->consignment_mitra_id,
                ]);

                $subtotalStore += $lineSubtotal;
            }

            // Process Rental items if any
            if (! empty($rentals)) {
                $rentalOrder = $this->rentalService->createRentalOrder(
                    [
                        'booking_id'           => $booking?->id,
                        'customer_name'        => $data['customer_name'] ?? 'Pendaki Kasir POS',
                        'customer_phone'       => $data['customer_phone'] ?? '-',
                        'id_card_deposit_type' => 'KTP',
                        'notes'                => "Sewa via Kasir POS Invoice {$invoiceNumber}",
                    ],
                    $rentals
                );

                $subtotalRental = (float) $rentalOrder->total_rental_fee;
                $rentalDeposit  = (float) $rentalOrder->total_deposit_amount;

                // Create POS order line-items for each rented asset
                foreach ($rentalOrder->items()->with('item')->get() as $orderItem) {
                    $rentalFee = (float) $orderItem->rate_per_day * $orderItem->rental_days;

                    PosOrderItem::create([
                        'pos_order_id' => $order->id,
                        'item_type'    => 'RENTAL',
                        'reference_id' => $orderItem->rental_item_id,
                        'item_name'    => "Sewa: {$orderItem->item->name} ({$orderItem->rental_days} malam)",
                        'qty'          => 1,
                        'unit_price'   => $rentalFee,
                        'subtotal'     => $rentalFee,
                        'cost_price'   => 0,
                    ]);

                    if ((float) $orderItem->item_deposit > 0) {
                        PosOrderItem::create([
                            'pos_order_id' => $order->id,
                            'item_type'    => 'RENTAL_DEPOSIT',
                            'reference_id' => $orderItem->rental_item_id,
                            'item_name'    => "Deposit Jaminan: {$orderItem->item->name}",
                            'qty'          => 1,
                            'unit_price'   => $orderItem->item_deposit,
                            'subtotal'     => $orderItem->item_deposit,
                            'cost_price'   => 0,
                        ]);
                    }
                }
            }

            // Process Ojek Tickets from Cashier Desk if any
            if (! empty($data['ojek_route_id'])) {
                $route = \App\Modules\Ojek\Models\OjekRoute::findOrFail($data['ojek_route_id']);
                $ojekQty = max(1, (int) ($data['ojek_qty'] ?? 1));
                $ojekSubtotal = (float) $route->fare_amount * $ojekQty;

                PosOrderItem::create([
                    'pos_order_id' => $order->id,
                    'item_type' => 'OJEK',
                    'reference_id' => $route->id,
                    'item_name' => "Tiket Ojek: {$route->route_name}",
                    'qty' => $ojekQty,
                    'unit_price' => $route->fare_amount,
                    'subtotal' => $ojekSubtotal,
                    'cost_price' => $route->driver_share,
                ]);

                // Otomatis masukkan ke antrean pangkalan ojek
                $ojekService = app(\App\Modules\Ojek\Services\OjekQueueService::class);
                for ($i = 0; $i < $ojekQty; $i++) {
                    $ojekService->createTicket([
                        'ojek_route_id' => $route->id,
                        'passenger_name' => ($data['customer_name'] ?? 'Pendaki Loket') . ($ojekQty > 1 ? " (#" . ($i + 1) . ")" : ""),
                        'passenger_phone' => $data['customer_phone'] ?? null,
                        'simaksi_booking_id' => $bookingId,
                        'pos_order_id' => $order->id,
                        'source' => 'LOKET_POS',
                        'payment_status' => 'PAID',
                        'payment_method' => $paymentMethod->value,
                        'notes' => "Invoice Kasir: {$invoiceNumber}",
                    ]);
                }

                $subtotalOjek += $ojekSubtotal;
            }

            $totalAmount = $subtotalSimaksi + $subtotalStore + $subtotalRental + $subtotalOjek + $ecoDeposit + $rentalDeposit;
            $paidAmount = max($totalAmount, (float) ($data['paid_amount'] ?? $totalAmount));
            $changeAmount = max(0, $paidAmount - $totalAmount);

            $order->update([
                'subtotal_simaksi' => $subtotalSimaksi,
                'subtotal_store' => $subtotalStore,
                'subtotal_rental' => $subtotalRental,
                'subtotal_ojek' => $subtotalOjek,
                'eco_deposit' => $ecoDeposit,
                'rental_deposit' => $rentalDeposit,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
            ]);

            return $order->fresh(['items.mitra', 'booking']);
        });
    }

    public function markPreorderPickup(string $code): array
    {
        $now = now();
        $code = trim($code);

        // 1. Cek berdasarkan Nomor Invoice POS Pre-order
        $order = PosOrder::with(['items', 'booking'])->where('invoice_number', $code)->first();
        if ($order) {
            $order->update([
                'is_preorder_pickup' => true,
                'notes' => trim(($order->notes ?? '') . ' [Pre-order telah diambil di loket kasir pada ' . $now->format('d/m H:i') . ']'),
            ]);

            if ($order->booking) {
                $order->booking->update([
                    'notes' => trim(($order->booking->notes ?? '') . ' [Pre-order telah diambil di loket kasir pada ' . $now->format('d/m H:i') . ']'),
                ]);
            }

            return [
                'success' => true,
                'order' => $order,
                'booking' => $order->booking,
                'message' => "Pre-order invoice {$order->invoice_number} atas nama {$order->customer_name} berhasil ditandai SUDAH DIAMBIL di loket.",
            ];
        }

        // 2. Cek berdasarkan Kode Booking atau QR Token SIMAKSI
        $booking = SimaksiBooking::where('booking_code', $code)
            ->orWhere('qr_token', $code)
            ->first();

        if ($booking) {
            $booking->update([
                'notes' => trim(($booking->notes ?? '') . ' [Pre-order telah diambil di loket kasir pada ' . $now->format('d/m H:i') . ']'),
            ]);

            return [
                'success' => true,
                'order' => null,
                'booking' => $booking,
                'message' => "Pre-order untuk rombongan {$booking->leader_name} ({$booking->booking_code}) berhasil ditandai SUDAH DIAMBIL di loket.",
            ];
        }

        throw new Exception("Kode '{$code}' tidak ditemukan pada sistem reservasi maupun invoice kasir.");
    }
}
