<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LostItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LostReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            $user = \App\Models\User::where('role', 'student')->first();
        }

        $categories = Category::all();
        $lostReports = $user ? LostItem::with('category')->where('user_id', $user->id)->latest()->get() : collect();

        return view('student.lost_reports', compact('lostReports', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required',
            'other_category' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_lost' => 'required|date',
            'location' => 'required|string|max:255',
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

        $user = Auth::user() ?? \App\Models\User::where('role', 'student')->first();

        $imagePath = null;
        $featureVector = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Save directly into public/images
            $file->move(public_path('images'), $filename);
            $imagePath = '/images/' . $filename;

            // Extract CNN feature vector via Python service
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

        LostItem::create([
            'user_id' => $user->id,
            'category_id' => $categoryId,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date_lost' => $validated['date_lost'],
            'location' => $validated['location'],
            'image_path' => $imagePath,
            'feature_vector' => $featureVector,
            'status' => 'open'
        ]);

        return redirect()->back()->with('success', 'Lost item report submitted and image saved to public/images!');
    }

    public function resolve($id)
    {
        $user = Auth::user() ?? \App\Models\User::where('role', 'student')->first();
        $item = LostItem::where('user_id', $user->id)->findOrFail($id);
        
        $item->status = 'resolved';
        $item->save();

        return redirect()->back()->with('success', 'Lost item report marked as resolved!');
    }

    public function scanCnn($id)
    {
        $user = Auth::user() ?? \App\Models\User::where('role', 'student')->first();
        $lostItem = LostItem::with('category')->where('user_id', $user->id)->findOrFail($id);

        $availableFound = \App\Models\FoundItem::with('category')
            ->whereIn('status', ['available', 'claim_pending'])
            ->get();

        $matches = [];

        foreach ($availableFound as $found) {
            $score = \App\Services\CNNEngineService::computeItemSimilarity($lostItem, $found);

            if ($score > 45.0) {
                $matches[] = [
                    'id' => $found->id,
                    'title' => $found->title,
                    'description' => $found->description,
                    'location' => $found->location,
                    'storage_location' => $found->storage_location,
                    'date_found' => $found->date_found->format('M d, Y'),
                    'image_path' => $found->image_path,
                    'score' => $score,
                    'status' => $found->status
                ];
            }
        }

        usort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return response()->json([
            'status' => 'success',
            'lost_item' => [
                'id' => $lostItem->id,
                'title' => $lostItem->title,
                'location' => $lostItem->location,
                'image_path' => $lostItem->image_path
            ],
            'matches' => $matches
        ]);
    }
}
