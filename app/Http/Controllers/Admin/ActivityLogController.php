<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->role === 'administrator' || ($request->user()->is_admin && in_array($request->user()->role, [null, 'customer'], true)), 403);
        $logs = AdminActivityLog::with('user:id,name,email')
            ->when($request->filled('action'), fn ($query) => $query->where('action', 'like', '%'.$request->string('action').'%'))
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
            ->latest()->paginate(30)->withQueryString();

        return view('admin.activity.index', compact('logs'));
    }
}
