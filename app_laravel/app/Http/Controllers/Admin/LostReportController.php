<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LostItem;

class LostReportController extends Controller
{
    public function index()
    {
        $allLostReports = LostItem::with(['category', 'user'])->latest()->get();

        return view('admin.lost_reports', compact('allLostReports'));
    }
}
