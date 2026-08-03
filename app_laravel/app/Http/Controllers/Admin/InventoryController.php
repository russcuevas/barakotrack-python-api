<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoundItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class InventoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $inventory = FoundItem::with(['category', 'user'])->latest()->get();

        return view('admin.inventory', compact('inventory', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required',
            'other_category' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_found' => 'required|date',
            'location' => 'required|string|max:255',
            'storage_location' => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
        ]);

        $categoryId = $request->category_id;
        if ($request->category_id === 'others' && $request->filled('other_category')) {
            $catName = trim($request->other_category);
            $cat = Category::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($catName)],
                [
                    'name' => $catName,
                    'icon' => 'bi-tag-fill',
                    'description' => 'User defined custom category'
                ]
            );
            $categoryId = $cat->id;
        }

        $admin = Auth::user() ?? \App\Models\User::where('role', 'admin')->first();

        $imagePath = null;
        $featureVector = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Save directly into public/images
            $file->move(public_path('images'), $filename);
            $imagePath = '/images/' . $filename;

            // Call Python service to extract CNN feature vector
            try {
                $fullPath = public_path('images/' . $filename);
                $response = Http::attach(
                    'image', file_get_contents($fullPath), $filename
                )->post('http://127.0.0.1:5000/extract-features');

                if ($response->successful()) {
                    $featureVector = $response->json('feature_vector');
                }
            } catch (\Exception $e) {
                // Feature extraction fallback
            }
        }

        FoundItem::create([
            'user_id' => $admin->id,
            'category_id' => $categoryId,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date_found' => $validated['date_found'],
            'location' => $validated['location'],
            'storage_location' => $validated['storage_location'],
            'image_path' => $imagePath,
            'feature_vector' => $featureVector,
            'status' => 'available'
        ]);

        return redirect()->back()->with('success', 'Found item registered into SAO inventory and saved to public/images!');
    }

    public function update(Request $request, $id)
    {
        $item = FoundItem::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:available,claim_pending,ready_for_pickup,claimed,disposed',
            'storage_location' => 'nullable|string|max:255'
        ]);

        $item->status = $validated['status'];
        if (!empty($validated['storage_location'])) {
            $item->storage_location = $validated['storage_location'];
        }
        $item->save();

        return redirect()->back()->with('success', "Found item inventory updated to '{$item->status}'.");
    }
}
