<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\Claim;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $activeRole = session('active_role', 'student');
        
        $studentUser = User::where('role', 'student')->first();
        $adminUser = User::where('role', 'admin')->first();

        // Statistics
        $lostItemsCount = LostItem::where('status', 'open')->count();
        $foundItemsCount = FoundItem::where('status', 'available')->count();
        $pendingClaimsCount = Claim::where('status', 'pending')->count();
        $claimedCount = FoundItem::where('status', 'claimed')->count();
        
        // Datasets
        $categories = Category::all();
        $recentFoundItems = FoundItem::with(['category', 'user'])->whereIn('status', ['available', 'claim_pending'])->latest()->get();
        $allFoundItems = FoundItem::with(['category', 'user'])->latest()->get();
        
        // Student-specific datasets
        $studentLostItems = $studentUser ? LostItem::with('category')->where('user_id', $studentUser->id)->latest()->get() : collect();
        $studentClaims = $studentUser ? Claim::with(['foundItem', 'lostItem'])->where('user_id', $studentUser->id)->latest()->get() : collect();
        
        // Admin-specific datasets
        $allClaims = Claim::with(['foundItem', 'lostItem', 'user', 'verifier'])->latest()->get();
        $allLostItems = LostItem::with(['category', 'user'])->latest()->get();

        return view('dashboard', compact(
            'activeRole',
            'studentUser',
            'adminUser',
            'lostItemsCount',
            'foundItemsCount',
            'pendingClaimsCount',
            'claimedCount',
            'categories',
            'recentFoundItems',
            'allFoundItems',
            'studentLostItems',
            'studentClaims',
            'allClaims',
            'allLostItems'
        ));
    }
}
