<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\User;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function storeLostItem(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_lost' => 'required|date',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
        ]);

        $student = User::where('role', 'student')->first() ?? User::first();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $imagePath = '/images/' . $filename;
        }

        LostItem::create([
            'user_id' => $student->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date_lost' => $validated['date_lost'],
            'location' => $validated['location'],
            'image_path' => $imagePath,
            'status' => 'open'
        ]);

        return redirect()->back()->with('success', 'Lost item report submitted successfully!');
    }

    public function storeFoundItem(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_found' => 'required|date',
            'location' => 'required|string|max:255',
            'storage_location' => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
        ]);

        $admin = User::where('role', 'admin')->first() ?? User::first();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $imagePath = '/images/' . $filename;
        }

        FoundItem::create([
            'user_id' => $admin->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date_found' => $validated['date_found'],
            'location' => $validated['location'],
            'storage_location' => $validated['storage_location'],
            'image_path' => $imagePath,
            'status' => 'available'
        ]);

        return redirect()->back()->with('success', 'Found item registered into SAO inventory successfully!');
    }

    public function updateFoundItemStatus(Request $request, $id)
    {
        $item = FoundItem::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:available,claim_pending,claimed,disposed',
            'storage_location' => 'nullable|string|max:255'
        ]);

        $item->status = $validated['status'];
        if (!empty($validated['storage_location'])) {
            $item->storage_location = $validated['storage_location'];
        }
        $item->save();

        return redirect()->back()->with('success', "Found item status updated to '{$item->status}'.");
    }

    public function resolveLostItem($id)
    {
        $item = LostItem::findOrFail($id);
        $item->status = 'resolved';
        $item->save();

        return redirect()->back()->with('success', 'Lost item report marked as resolved!');
    }
}
