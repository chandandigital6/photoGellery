<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPlan;
use App\Models\UserUsage;
use App\Models\UploadedImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUsageController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->with(['activePlan'])
            ->withCount([
                'uploadedImages as total_uploaded_images',
                'uploadedImages as total_generated_images' => function ($q) {
                    $q->whereNotNull('generated_image');
                },
            ])
            ->latest()
            ->get()
            ->map(function ($user) {
                $plan = $user->activePlan;

                $usedBytes = UserUsage::where('user_id', $user->id)->sum('used_bytes');
                $limitBytes = $plan ? $plan->limit_mb * 1024 * 1024 : 0;

                $user->used_mb = round($usedBytes / 1024 / 1024, 2);
                $user->limit_mb = $plan?->limit_mb ?? 0;
                $user->remaining_mb = max(0, round(($limitBytes - $usedBytes) / 1024 / 1024, 2));
                $user->plan_type = $plan?->limit_type ?? 'No Plan';

                return $user;
            });

        return view('admin.usage.index', compact('users'));
    }

    public function show(User $user)
    {
        $plan = UserPlan::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->first();

        $usedBytes = UserUsage::where('user_id', $user->id)->sum('used_bytes');
        $limitBytes = $plan ? $plan->limit_mb * 1024 * 1024 : 0;

        $summary = [
            'limit_mb' => $plan?->limit_mb ?? 0,
            'used_mb' => round($usedBytes / 1024 / 1024, 2),
            'remaining_mb' => max(0, round(($limitBytes - $usedBytes) / 1024 / 1024, 2)),
            'plan_type' => $plan?->limit_type ?? 'No Plan',
            'start_date' => $plan?->start_date,
            'end_date' => $plan?->end_date,
        ];

        $dateWise = UploadedImage::where('user_id', $user->id)
            ->selectRaw('DATE(upload_date) as date')
            ->selectRaw('COUNT(*) as total_uploaded')
            ->selectRaw('SUM(CASE WHEN generated_image IS NOT NULL THEN 1 ELSE 0 END) as total_generated')
            ->groupBy(DB::raw('DATE(upload_date)'))
            ->orderByDesc('date')
            ->get();

        return view('admin.usage.show', compact('user', 'summary', 'dateWise'));
    }

    public function dateWise(User $user, $date)
    {
        $images = UploadedImage::where('user_id', $user->id)
            ->whereDate('upload_date', $date)
            ->latest()
            ->get();

        return view('admin.usage.date', compact('user', 'date', 'images'));
    }
}