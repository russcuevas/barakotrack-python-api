<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FoundItem;
use App\Models\Category;
use Illuminate\Http\Request;

class FoundItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        
        $query = FoundItem::with(['category', 'user'])->whereIn('status', ['available', 'claim_pending']);

        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $foundItems = $query->latest()->get();

        return view('student.found_items', compact('foundItems', 'categories'));
    }
}
