<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\View\View;

class AdminActivityController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::with('user')->latest()->paginate(25);

        return view('admin.activity.index', compact('logs'));
    }
}
