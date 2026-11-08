<?php

use App\Modules\Admin\Controllers\AdminController;
use App\Modules\Admin\Controllers\CheckpointPhotoController;
use App\Modules\Admin\Controllers\InventoryController;
use App\Modules\Admin\Controllers\SettingController;
use App\Modules\Admin\Controllers\UserController;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Consignment\Controllers\ConsignmentController;
use App\Modules\FieldOps\Controllers\FieldOpsController;
use App\Modules\Ojek\Controllers\OjekQueueController;
use App\Modules\Ojek\Controllers\PublicOjekController;
use App\Modules\POS\Controllers\PosController;
use App\Modules\Rental\Controllers\PublicRentalController;
use App\Modules\Rental\Controllers\RentalController;
use App\Modules\Simaksi\Controllers\BookingController;
use App\Modules\Simaksi\Controllers\PublicBookingCheckController;
use App\Modules\Simaksi\Controllers\PublicTrailMapController;
use App\Modules\Simaksi\Models\DailyQuota;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Modules\Store\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

// ==========================================
// A. PUBLIC PAGES (Guest Friendly & Frictionless)
// ==========================================

// 1. Landing Portal Cikuray via Cigedug
Route::get('/', function () {
    $trailSetting = TrailSetting::current();
    $todayQuota = DailyQuota::where('date', today()->toDateString())->first();
    $maxQuota = $todayQuota ? $todayQuota->max_quota : (today()->isWeekend() ? 300 : 150);
    $remainingQuota = $todayQuota ? $todayQuota->remaining_quota : $maxQuota;
    $checkpoints = \App\Modules\Simaksi\Models\Checkpoint::with('photos')->where('is_active', true)->orderBy('order')->get();

    return view('home', compact('trailSetting', 'remainingQuota', 'maxQuota', 'checkpoints'));
})->name('home');

// 2. Auth Routes (Staf & Petugas Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// 3. Peta Satelit & Trek GPS Gunung Cikuray (/peta & /peta-jalur)
Route::get('/peta', [PublicTrailMapController::class, 'index'])->name('trail.map');
Route::get('/peta-jalur', function () {
    return redirect()->route('trail.map');
})->name('peta.index');

// 4. Public Rental Catalog (/sewa)
Route::get('/sewa', [PublicRentalController::class, 'index'])->name('rental.catalog');

// 4. Public Self-Service "Cek Booking & E-Ticket" (/cek-booking)
Route::get('/cek-booking', [PublicBookingCheckController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('simaksi.check');

// 5. Modul SIMAKSI & Manifest (Public)
Route::prefix('simaksi')->name('simaksi.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/booking', [BookingController::class, 'create'])->name('create');
    Route::post('/booking', [BookingController::class, 'store'])
        ->middleware('throttle:15,1')
        ->name('store');
    Route::get('/ticket/{code}', [BookingController::class, 'show'])->name('show');
    Route::post('/ticket/{code}/payment-proof', [BookingController::class, 'uploadPaymentProof'])
        ->middleware('throttle:10,1')
        ->name('upload-proof');
    Route::get('/api/quota', [BookingController::class, 'checkQuota'])
        ->middleware('throttle:60,1')
        ->name('check-quota');
});

// 6. Modul Toko & Pre-Order Omnichannel (Public)
Route::prefix('store')->name('store.')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('index');
    Route::post('/preorder', [StoreController::class, 'preorder'])
        ->middleware('throttle:15,1')
        ->name('preorder');
    Route::get('/success/{invoice}', [StoreController::class, 'success'])->name('success');
});

// 7. Modul Antrean & Tarif Ojek Paguyuban Cigedug (Public)
Route::prefix('ojek')->name('ojek.public.')->group(function () {
    Route::get('/', [PublicOjekController::class, 'publicView'])->name('index');
    Route::get('/display', [PublicOjekController::class, 'display'])->name('display');
    Route::get('/api/display', [PublicOjekController::class, 'displayApi'])->name('api.display');
    Route::get('/api/ticket/check', [PublicOjekController::class, 'checkTicket'])->name('api.ticket-check');
});


// ==========================================
// B. PROTECTED INTERNAL ROUTES (RBAC Staff & Rangers)
// ==========================================

// 1. Modul POS Kasir Terpadu (Superadmin, Kasir)
Route::prefix('pos')->name('pos.')->middleware(['auth', 'role:SUPERADMIN,KASIR'])->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::get('/simaksi/search', [PosController::class, 'searchBooking'])->name('simaksi.search');
    Route::post('/simaksi/walkin', [PosController::class, 'storeWalkInBooking'])->name('simaksi.walkin');
    Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
    Route::get('/invoice/{invoiceNumber}', [PosController::class, 'invoice'])->name('invoice');
    Route::post('/pickup', [PosController::class, 'pickupScan'])->name('pickup');
    Route::get('/shift', [PosController::class, 'shiftReconciliation'])->name('shift');
    Route::post('/shift/open', [PosController::class, 'openShift'])->name('shift.open');
    Route::post('/shift/close/{id}', [PosController::class, 'closeShift'])->name('shift.close');
});

// 2. Modul Rental Gudang & QC (Superadmin, Kasir)
Route::prefix('rental')->name('rental.')->middleware(['auth', 'role:SUPERADMIN,KASIR'])->group(function () {
    Route::get('/', [RentalController::class, 'index'])->name('index');
    Route::post('/{id}/status', [RentalController::class, 'updateStatus'])->name('update-status');
    Route::post('/{id}/qc', [RentalController::class, 'returnQc'])->name('return-qc');
});

// 3. Modul Konsinyasi UMKM Lokal (Superadmin, Admin SAR)
Route::prefix('consignment')->name('consignment.')->middleware(['auth', 'role:SUPERADMIN,ADMIN_SAR'])->group(function () {
    Route::get('/', [ConsignmentController::class, 'index'])->name('index');
    Route::post('/settlement', [ConsignmentController::class, 'generateSettlement'])->name('settlement');
    Route::get('/settlement/{id}', [ConsignmentController::class, 'showSettlement'])->name('settlement.show');
    Route::post('/settlement/{id}/confirm', [ConsignmentController::class, 'confirmSettlement'])->name('settlement.confirm');
});

// 4. Modul FieldOps PWA Checkpoint Scanner (Superadmin, Admin SAR, Petugas Pos 1-4)
Route::prefix('fieldops')->name('fieldops.')->middleware(['auth', 'role:SUPERADMIN,ADMIN_SAR,PETUGAS_POS'])->group(function () {
    Route::get('/', [FieldOpsController::class, 'index'])->name('index');
    Route::post('/scan', [FieldOpsController::class, 'scan'])->name('scan');
    Route::post('/sync', [FieldOpsController::class, 'syncOffline'])->name('sync');
    Route::post('/sos', [FieldOpsController::class, 'sosAlert'])->name('sos');
    Route::post('/waste', [FieldOpsController::class, 'verifyWaste'])->name('waste');
});

// 5. Modul Adaptive Admin Command Center (Superadmin, Admin SAR)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:SUPERADMIN,ADMIN_SAR'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('/trail-control', [AdminController::class, 'emergencyTrailControl'])->name('trail-control');
    Route::get('/tracker', [AdminController::class, 'posTracker'])->name('tracker');
    Route::get('/sar-export/{id}', [AdminController::class, 'sarExport'])->name('sar-export');
    Route::get('/finance', [AdminController::class, 'financialReport'])->name('finance');
    Route::get('/quota', [AdminController::class, 'quotaManager'])->name('quota');
    Route::post('/quota/update', [AdminController::class, 'updateQuota'])->name('quota.update');

    // 6. Analitik & Statistik (Tren Pendaki, Heatmap, Conversion Rate, Top Produk)
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');

    // 7. CRUD Foto Dokumentasi Pos / Checkpoint
    Route::prefix('checkpoints')->name('checkpoints.')->group(function () {
        Route::get('/', [CheckpointPhotoController::class, 'index'])->name('index');
        Route::get('/{id}', [CheckpointPhotoController::class, 'show'])->name('show');
        Route::post('/{id}/photos', [CheckpointPhotoController::class, 'store'])->name('photos.store');
        Route::put('/photos/{id}', [CheckpointPhotoController::class, 'update'])->name('photos.update');
        Route::post('/photos/{id}/primary', [CheckpointPhotoController::class, 'setPrimary'])->name('photos.primary');
        Route::delete('/photos/{id}', [CheckpointPhotoController::class, 'destroy'])->name('photos.destroy');
    });

    // Konfirmasi Pembayaran Manual & Registrasi Walk-In Admin
    Route::get('/bookings/pending', [AdminController::class, 'pendingBookings'])->name('bookings.pending');
    Route::post('/bookings/walkin', [AdminController::class, 'storeWalkInBooking'])->name('bookings.walkin');
    Route::post('/bookings/{id}/confirm', [AdminController::class, 'confirmBookingPayment'])->name('bookings.confirm');
    Route::post('/bookings/{id}/reject', [AdminController::class, 'rejectBookingPayment'])->name('bookings.reject');

    // 8. Modul Manajemen Staf & Pengguna (RBAC)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // 9. Modul Katalog Produk Toko & Alat Sewa Outdoor
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('/products', [InventoryController::class, 'storeProduct'])->name('products.store');
        Route::put('/products/{id}', [InventoryController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{id}', [InventoryController::class, 'destroyProduct'])->name('products.destroy');
        Route::post('/rentals', [InventoryController::class, 'storeRental'])->name('rentals.store');
        Route::put('/rentals/{id}', [InventoryController::class, 'updateRental'])->name('rentals.update');
        Route::delete('/rentals/{id}', [InventoryController::class, 'destroyRental'])->name('rentals.destroy');
    });

    // 10. Modul Pengaturan Tarif SIMAKSI & Operasional Jalur
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'update'])->name('update');
    });
});

// 8. Modul Sistem Antrian Ojek Basecamp (Internal Staf & Pangkalan)
Route::prefix('ojek')->name('ojek.')->middleware(['auth', 'role:SUPERADMIN,KASIR,ADMIN_SAR'])->group(function () {
    Route::get('/antrean', [OjekQueueController::class, 'index'])->name('index');
    Route::post('/call-next', [OjekQueueController::class, 'callNext'])->name('call-next');
    Route::post('/trip/{id}/start', [OjekQueueController::class, 'startTrip'])->name('trip.start');
    Route::post('/trip/{id}/complete', [OjekQueueController::class, 'completeTrip'])->name('trip.complete');
    Route::post('/ticket/{id}/cancel', [OjekQueueController::class, 'cancelTicket'])->name('ticket.cancel');
    Route::post('/checkin', [OjekQueueController::class, 'checkInDriver'])->name('checkin');
    Route::post('/attendance/{id}/status', [OjekQueueController::class, 'updateDriverStatus'])->name('attendance.status');
    Route::post('/walkin', [OjekQueueController::class, 'storeWalkIn'])->name('walkin');
    Route::get('/api/realtime', [OjekQueueController::class, 'realtimeApi'])->name('api.realtime');
    Route::get('/drivers', [OjekQueueController::class, 'drivers'])->name('drivers');
    Route::post('/drivers', [OjekQueueController::class, 'storeDriver'])->name('drivers.store');
    Route::post('/drivers/{id}/toggle', [OjekQueueController::class, 'toggleDriverActive'])->name('drivers.toggle');
});
