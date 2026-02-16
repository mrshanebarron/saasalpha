<?php

namespace App\Http\Controllers;

use App\Models\{CpdRecord, User};

class CpdController extends Controller
{
    public function index()
    {
        $tid = auth()->user()->tenant_id;
        $records = CpdRecord::where('tenant_id', $tid)->with(['user', 'verifiedBy'])->latest('completed_date')->get();

        $userSummary = User::where('tenant_id', $tid)->get()->map(function ($user) {
            $cpd = $user->cpdRecords;
            return (object)[
                'user' => $user,
                'total_hours' => $cpd->sum('hours'),
                'verified_hours' => $cpd->where('verified', true)->sum('hours'),
                'records_count' => $cpd->count(),
            ];
        });

        return view('cpd.index', compact('records', 'userSummary'));
    }
}
