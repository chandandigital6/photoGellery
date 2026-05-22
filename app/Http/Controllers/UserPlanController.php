<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\Request;

class UserPlanController extends Controller
{
    public function edit(User $user)
    {
        $plan = UserPlan::where('user_id', $user->id)
            ->latest()
            ->first();

        return view('users.plan', compact('user', 'plan'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'limit_mb' => ['required', 'integer', 'min:1'],
            'limit_type' => ['required', 'in:daily,monthly,yearly'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        UserPlan::where('user_id', $user->id)->update([
            'is_active' => false,
        ]);

        UserPlan::create([
            'user_id' => $user->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'limit_mb' => $request->limit_mb,
            'limit_type' => $request->limit_type,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User plan updated successfully.');
    }
    
}