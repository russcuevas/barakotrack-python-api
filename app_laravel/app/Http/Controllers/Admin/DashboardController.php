<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
        $readyForPickupCount = FoundItem::where('status', 'ready_for_pickup')->count();
        $claimedReportsCount = FoundItem::where('status', 'claimed')->count();

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

        // Chart Data 1: Category Analytics (Found Items vs Lost Reports per Category)
        $categories = Category::withCount(['foundItems', 'lostItems'])->get();
        $categoryLabels = $categories->pluck('name');
        $foundCategoryCounts = $categories->pluck('found_items_count');
        $lostCategoryCounts = $categories->pluck('lost_items_count');

        // Chart Data 2: Inventory Status Breakdown
        $disposedCount = FoundItem::where('status', 'disposed')->count();
        $claimPendingFoundCount = FoundItem::where('status', 'claim_pending')->count();

        $statusLabels = ['Available', 'Claim Pending', 'Ready for Pick-up', 'Claimed', 'Disposed'];
        $statusCounts = [
            $storageCount,
            $claimPendingFoundCount,
            $readyForPickupCount,
            $claimedReportsCount,
            $disposedCount
        ];

        // Chart Data 3: Yearly Breakdown (Lost Reports, Found Items & Percentage Share)
        $allLostItems = LostItem::select('date_lost', 'created_at')->get();
        $allFoundItems = FoundItem::select('date_found', 'created_at')->get();

        $yearlyLostCounts = [];
        $yearlyFoundCounts = [];

        foreach ($allLostItems as $item) {
            $year = $item->date_lost ? $item->date_lost->format('Y') : $item->created_at->format('Y');
            $yearlyLostCounts[$year] = ($yearlyLostCounts[$year] ?? 0) + 1;
        }

        foreach ($allFoundItems as $item) {
            $year = $item->date_found ? $item->date_found->format('Y') : $item->created_at->format('Y');
            $yearlyFoundCounts[$year] = ($yearlyFoundCounts[$year] ?? 0) + 1;
        }

        $allYears = array_unique(array_merge(array_keys($yearlyLostCounts), array_keys($yearlyFoundCounts)));
        if (empty($allYears)) {
            $allYears = [(string)date('Y')];
        }
        sort($allYears);

        $totalLostAllYears = array_sum($yearlyLostCounts) ?: 1;

        $yearlyLabels = [];
        $yearlyLostData = [];
        $yearlyFoundData = [];
        $yearlyLostPercentage = [];
        $maxPct = -1;
        $peakYearInfo = 'N/A';

        foreach ($allYears as $yr) {
            $lostCount = $yearlyLostCounts[$yr] ?? 0;
            $foundCount = $yearlyFoundCounts[$yr] ?? 0;
            $pct = round(($lostCount / $totalLostAllYears) * 100, 1);

            $yearlyLabels[] = $yr;
            $yearlyLostData[] = $lostCount;
            $yearlyFoundData[] = $foundCount;
            $yearlyLostPercentage[] = $pct;

            if ($pct > $maxPct) {
                $maxPct = $pct;
                $peakYearInfo = "{$yr} ({$pct}% of total lost)";
            }
        }

        // Chart Data 4: Real-time Campus Hazard Radar (strictly based on actual DB location values)
        $lostLocations = LostItem::select('location', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->groupBy('location')
            ->orderBy('count', 'desc')
            ->get();

        $foundLocations = FoundItem::select('location', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->groupBy('location')
            ->orderBy('count', 'desc')
            ->get();

        $lostLocMap = [];
        foreach ($lostLocations as $item) {
            $loc = trim($item->location);
            $lostLocMap[$loc] = ($lostLocMap[$loc] ?? 0) + $item->count;
        }

        $foundLocMap = [];
        foreach ($foundLocations as $item) {
            $loc = trim($item->location);
            $foundLocMap[$loc] = ($foundLocMap[$loc] ?? 0) + $item->count;
        }

        $allDbLocations = array_unique(array_merge(array_keys($lostLocMap), array_keys($foundLocMap)));

        usort($allDbLocations, function($a, $b) use ($lostLocMap, $foundLocMap) {
            $totalA = ($lostLocMap[$a] ?? 0) + ($foundLocMap[$a] ?? 0);
            $totalB = ($lostLocMap[$b] ?? 0) + ($foundLocMap[$b] ?? 0);
            return $totalB <=> $totalA;
        });

        $allDbLocations = array_slice($allDbLocations, 0, 7);

        if (empty($allDbLocations)) {
            $allDbLocations = ['No Locations Recorded'];
            $lostLocMap['No Locations Recorded'] = 0;
            $foundLocMap['No Locations Recorded'] = 0;
        }

        $locationLabels = array_values($allDbLocations);
        $locationLostCounts = [];
        $locationFoundCounts = [];

        foreach ($locationLabels as $loc) {
            $locationLostCounts[] = $lostLocMap[$loc] ?? 0;
            $locationFoundCounts[] = $foundLocMap[$loc] ?? 0;
        }

        $totalStudentsCount = \App\Models\User::where('role', 'student')->count();
        $resolvedClaimsCount = Claim::whereIn('status', ['approved', 'completed'])->count();

        return view('admin.dashboard', compact(
            'storageCount',
            'pendingClaimsCount',
            'readyForPickupCount',
            'claimedReportsCount',
            'recentPendingClaims',
            'recentInventory',
            'categoryLabels',
            'foundCategoryCounts',
            'lostCategoryCounts',
            'statusLabels',
            'statusCounts',
            'yearlyLabels',
            'yearlyLostData',
            'yearlyFoundData',
            'yearlyLostPercentage',
            'peakYearInfo',
            'locationLabels',
            'locationLostCounts',
            'locationFoundCounts',
            'totalStudentsCount',
            'resolvedClaimsCount'
        ));
    }
}
