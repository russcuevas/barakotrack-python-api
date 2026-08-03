<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\CNNEngineService;

class ClaimVerificationController extends Controller
{
    public function index()
    {
        $pendingClaims = Claim::with(['foundItem', 'lostItem', 'user'])->where('status', 'pending')->latest()->get();
        $processedClaims = Claim::with(['foundItem', 'lostItem', 'user', 'verifier'])->whereIn('status', ['approved', 'rejected'])->latest()->get();

        foreach ($pendingClaims as $claim) {
            $claim->match_score = CNNEngineService::computeClaimSimilarity($claim);
        }

        foreach ($processedClaims as $claim) {
            $claim->match_score = CNNEngineService::computeClaimSimilarity($claim);
        }

        return view('admin.claims', compact('pendingClaims', 'processedClaims'));
    }

    public function approve(Request $request, $id)
    {
        $claim = Claim::findOrFail($id);
        $admin = Auth::user() ?? \App\Models\User::where('role', 'admin')->first();

        $adminNotes = $request->input('admin_notes', 'Proof verified by SAO Staff. Ready for pickup.');

        $claim->status = 'approved';
        $claim->admin_notes = $adminNotes;
        $claim->verified_by = $admin->id;
        $claim->save();

        if ($claim->foundItem) {
            $claim->foundItem->status = 'ready_for_pickup';
            $claim->foundItem->save();
        }

        if ($claim->lostItem) {
            $claim->lostItem->status = 'claim_pending';
            $claim->lostItem->save();
        }

        return redirect()->back()->with('success', "Claim #{$claim->id} APPROVED! Found item tagged as 'Ready for Pick-up'.");
    }

    public function markClaimed(Request $request, $id)
    {
        $claim = Claim::findOrFail($id);

        if ($claim->foundItem) {
            $claim->foundItem->status = 'claimed';
            $claim->foundItem->save();
        }

        if ($claim->lostItem) {
            $claim->lostItem->status = 'resolved';
            $claim->lostItem->save();
        }

        return redirect()->back()->with('success', "Claim #{$claim->id} item marked as Picked Up & Claimed!");
    }

    public function reject(Request $request, $id)
    {
        $claim = Claim::findOrFail($id);
        $admin = Auth::user() ?? \App\Models\User::where('role', 'admin')->first();

        $adminNotes = $request->input('admin_notes', 'Proof details do not match item specifications.');

        $claim->status = 'rejected';
        $claim->admin_notes = $adminNotes;
        $claim->verified_by = $admin->id;
        $claim->save();

        if ($claim->foundItem) {
            $claim->foundItem->status = 'available';
            $claim->foundItem->save();
        }

        if ($claim->lostItem) {
            $claim->lostItem->status = 'open';
            $claim->lostItem->save();
        }

        return redirect()->back()->with('warning', "Claim #{$claim->id} REJECTED.");
    }
}
