<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\LostItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    public function index()
    {
        $user = Auth::user() ?? \App\Models\User::where('role', 'student')->first();
        $claims = $user ? Claim::with(['foundItem', 'lostItem'])->where('user_id', $user->id)->latest()->get() : collect();

        return view('student.claims', compact('claims'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'found_item_id' => 'required|exists:found_items,id',
            'proof_description' => 'required|string|min:10',
            'proof_image' => 'nullable|image|max:5120',
            'lost_item_id' => 'nullable|exists:lost_items,id'
        ]);

        $user = Auth::user() ?? \App\Models\User::where('role', 'student')->first();
        $foundItem = FoundItem::findOrFail($validated['found_item_id']);

        $proofImagePath = null;
        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $filename = 'claim_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Save directly into public/images
            $file->move(public_path('images'), $filename);
            $proofImagePath = '/images/' . $filename;
        }

        $claim = Claim::create([
            'found_item_id' => $foundItem->id,
            'lost_item_id' => $validated['lost_item_id'] ?? null,
            'user_id' => $user->id,
            'proof_description' => $validated['proof_description'],
            'proof_image' => $proofImagePath,
            'status' => 'pending'
        ]);

        $foundItem->status = 'claim_pending';
        $foundItem->save();

        if ($claim->lost_item_id) {
            $lostItem = LostItem::find($claim->lost_item_id);
            if ($lostItem) {
                $lostItem->status = 'claim_pending';
                $lostItem->save();
            }
        }

        return redirect()->route('student.claims')->with('success', 'Claim request submitted! Proof document saved to public/images.');
    }
}
