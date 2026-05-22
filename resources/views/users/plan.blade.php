<x-layouts::app :title="__('User Plan')">

    <div class="mx-auto max-w-3xl p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Set User Plan</h1>
            <p class="text-sm text-gray-500">
                User: <strong>{{ $user->name }}</strong> — {{ $user->email }}
            </p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow border">
            <form method="POST" action="{{ route('users.plan.update', $user->id) }}" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-medium">Plan Start Date</label>
                    <input type="date"
                           name="start_date"
                           value="{{ old('start_date', optional($plan?->start_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                           class="w-full rounded-lg border px-4 py-2">
                    @error('start_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Plan End Date</label>
                    <input type="date"
                           name="end_date"
                           value="{{ old('end_date', optional($plan?->end_date)->format('Y-m-d') ?? now()->addMonth()->format('Y-m-d')) }}"
                           class="w-full rounded-lg border px-4 py-2">
                    @error('end_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Upload Limit MB</label>
                    <input type="number"
                           name="limit_mb"
                           value="{{ old('limit_mb', $plan->limit_mb ?? 100) }}"
                           min="1"
                           class="w-full rounded-lg border px-4 py-2"
                           placeholder="Example: 500">
                    @error('limit_mb') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Limit Type</label>
                    <select name="limit_type" class="w-full rounded-lg border px-4 py-2">
                        <option value="daily" @selected(old('limit_type', $plan->limit_type ?? '') === 'daily')>Daily</option>
                        <option value="monthly" @selected(old('limit_type', $plan->limit_type ?? 'monthly') === 'monthly')>Monthly</option>
                        <option value="yearly" @selected(old('limit_type', $plan->limit_type ?? '') === 'yearly')>Yearly</option>
                    </select>
                    @error('limit_type') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="text-sm">Plan Active</span>
                </label>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('users.index') }}"
                       class="rounded-lg border px-4 py-2">
                        Back
                    </a>

                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2 font-semibold text-white hover:bg-blue-700">
                        Save Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts::app>