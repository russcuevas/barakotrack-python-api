<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\Claim;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            $user = \App\Models\User::where('role', 'student')->first();
        }

        $lostItemsCount = LostItem::where('status', 'open')->count();
        $foundItemsCount = FoundItem::where('status', 'available')->count();
        $pendingClaimsCount = $user ? Claim::where('user_id', $user->id)->where('status', 'pending')->count() : 0;
        
        $categories = Category::all();
        $recentFoundItems = FoundItem::with(['category', 'user'])
            ->whereIn('status', ['available', 'claim_pending'])
            ->latest()
            ->take(3)
            ->get();

        $studentLostItems = $user ? LostItem::with('category')->where('user_id', $user->id)->latest()->take(3)->get() : collect();
        $studentClaims = $user ? Claim::with(['foundItem', 'lostItem'])->where('user_id', $user->id)->latest()->take(3)->get() : collect();

        return view('student.dashboard', compact(
            'user',
            'lostItemsCount',
            'foundItemsCount',
            'pendingClaimsCount',
            'categories',
            'recentFoundItems',
            'studentLostItems',
            'studentClaims'
        ));
    }
}
