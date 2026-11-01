<?php

namespace App\Modules\Rental\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Rental\Models\RentalItem;
use App\Shared\Enums\RentalAssetStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicRentalController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCategory = $request->input('category', 'ALL');

        $query = RentalItem::query();

        if ($selectedCategory !== 'ALL') {
            $query->where('category', $selectedCategory);
        }

        // Get items ordered by category and name
        $items = $query->orderBy('category')->orderBy('name')->get();

        // Summary stats
        $totalAvailable = RentalItem::where('status', RentalAssetStatus::TERSEDIA)->count();
        $totalItems = RentalItem::count();

        // Distinct categories for tabs
        $categories = RentalItem::select('category')->distinct()->pluck('category');

        return view('rental.catalog', compact(
            'items',
            'selectedCategory',
            'categories',
            'totalAvailable',
            'totalItems'
        ));
    }
}
