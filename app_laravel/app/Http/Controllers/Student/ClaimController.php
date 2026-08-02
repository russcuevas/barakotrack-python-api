<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\LostItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    public function index()
    {
        $user = Auth::user() ?? User::where('role', 'student')->first();
        $claims = $user ? Claim::with(['foundItem', 'lostItem'])->where('user_id', $user->id)->latest()->get() : collect();

        return view('student.claims', compact('claims'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'found_item_id' => 'required|exists:found_items,id',
            'proof_description' => 'required|string|min:3',
            'proof_image' => 'nullable|image|max:5120',
            'lost_item_id' => 'nullable|exists:lost_items,id'
        ]);

        $user = Auth::user() ?? User::where('role', 'student')->first();

        if (!$user) {
            return redirect()->back()->withErrors(['user' => 'Student account not found. Please log in first.']);
        }

        $foundItem = FoundItem::findOrFail($validated['found_item_id']);

        $proofImagePath = null;
        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $filename = 'claim_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Save directly into public/images
            $file->move(public_path('images'), $filename);
            $proofImagePath = '/images/' . $filename;
        }

        $lostItemId = $validated['lost_item_id'] ?? null;
        if (!$lostItemId) {
            $matchingLost = LostItem::where('user_id', $user->id)
                ->where('category_id', $foundItem->category_id)
                ->whereIn('status', ['open', 'claim_pending'])
                ->first();
            if ($matchingLost) {
                $lostItemId = $matchingLost->id;
            }
        }

        $claim = Claim::create([
            'found_item_id' => $foundItem->id,
            'lost_item_id' => $lostItemId,
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

        return redirect()->route('student.claims')->with('success', 'Claim request submitted successfully! SAO Admin will verify your proof of ownership.');
    }
}
