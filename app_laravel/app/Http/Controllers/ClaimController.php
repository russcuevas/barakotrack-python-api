<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\LostItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'found_item_id' => 'required|exists:found_items,id',
            'proof_description' => 'required|string|min:10',
            'proof_image' => 'nullable|image|max:5120',
            'lost_item_id' => 'nullable|exists:lost_items,id'
        ]);

        $student = User::where('role', 'student')->first() ?? User::first();
        $foundItem = FoundItem::findOrFail($validated['found_item_id']);

        $proofImagePath = null;
        if ($request->hasFile('proof_image')) {
            $path = $request->file('proof_image')->store('claims', 'public');
            $proofImagePath = Storage::url($path);
        }

        $claim = Claim::create([
            'found_item_id' => $foundItem->id,
            'lost_item_id' => $validated['lost_item_id'] ?? null,
            'user_id' => $student->id,
            'proof_description' => $validated['proof_description'],
            'proof_image' => $proofImagePath,
            'status' => 'pending'
        ]);

        // Update found item status
        $foundItem->status = 'claim_pending';
        $foundItem->save();

        if ($claim->lost_item_id) {
            $lostItem = LostItem::find($claim->lost_item_id);
            if ($lostItem) {
                $lostItem->status = 'claim_pending';
                $lostItem->save();
            }
        }

        return redirect()->back()->with('success', 'Claim request submitted! SAO Admin will verify your proof of ownership.');
    }

    public function approve(Request $request, $id)
    {
        $claim = Claim::findOrFail($id);
        $admin = User::where('role', 'admin')->first() ?? User::first();

        $adminNotes = $request->input('admin_notes', 'Proof of ownership verified by SAO Staff.');

        $claim->status = 'approved';
        $claim->admin_notes = $adminNotes;
        $claim->verified_by = $admin->id;
        $claim->save();

        // Update found item to claimed
        if ($claim->foundItem) {
            $claim->foundItem->status = 'claimed';
            $claim->foundItem->save();
        }

        // Update lost item to resolved
        if ($claim->lostItem) {
            $claim->lostItem->status = 'resolved';
            $claim->lostItem->save();
        }

        return redirect()->back()->with('success', "Claim #{$claim->id} APPROVED! Item marked as Claimed.");
    }

    public function reject(Request $request, $id)
    {
        $claim = Claim::findOrFail($id);
        $admin = User::where('role', 'admin')->first() ?? User::first();

        $adminNotes = $request->input('admin_notes', 'Proof details submitted do not match the item specifications.');

        $claim->status = 'rejected';
        $claim->admin_notes = $adminNotes;
        $claim->verified_by = $admin->id;
        $claim->save();

        // Revert found item status back to available
        if ($claim->foundItem) {
            $claim->foundItem->status = 'available';
            $claim->foundItem->save();
        }

        // Revert lost item status back to open
        if ($claim->lostItem) {
            $claim->lostItem->status = 'open';
            $claim->lostItem->save();
        }

        return redirect()->back()->with('warning', "Claim #{$claim->id} REJECTED. Item returned to Available inventory.");
    }
}
