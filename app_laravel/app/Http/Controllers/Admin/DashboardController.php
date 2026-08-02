<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoundItem;
use App\Models\Claim;
use App\Models\LostItem;

use App\Services\CNNEngineService;

class DashboardController extends Controller
{
    public function index()
    {
        $storageCount = FoundItem::where('status', 'available')->count();
        $pendingClaimsCount = Claim::where('status', 'pending')->count();
        $approvedClaimsCount = Claim::where('status', 'approved')->count();
        $totalLostReports = LostItem::count();

        $recentPendingClaims = Claim::with(['foundItem', 'lostItem', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->take(4)
            ->get();

        foreach ($recentPendingClaims as $claim) {
            $claim->match_score = CNNEngineService::computeClaimSimilarity($claim);
        }

        $recentInventory = FoundItem::with(['category', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'storageCount',
            'pendingClaimsCount',
            'approvedClaimsCount',
            'totalLostReports',
            'recentPendingClaims',
            'recentInventory'
        ));
    }
}
