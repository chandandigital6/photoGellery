<x-layouts::app :title="__('User Usage Detail')">

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ $user->name }} Usage</h1>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>

            <a href="{{ route('admin.usage.index') }}"
               class="rounded-lg border px-4 py-2 text-sm">
                Back
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Plan</p>
                <h3 class="text-xl font-bold uppercase">{{ $summary['plan_type'] }}</h3>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Limit</p>
                <h3 class="text-xl font-bold">{{ $summary['limit_mb'] }} MB</h3>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Used</p>
                <h3 class="text-xl font-bold text-orange-600">{{ $summary['used_mb'] }} MB</h3>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Remaining</p>
                <h3 class="text-xl font-bold text-green-600">{{ $summary['remaining_mb'] }} MB</h3>
            </div>
        </div>

        <div class="rounded-2xl border bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-xl font-bold">Date Wise Generated Images</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Uploaded Images</th>
                            <th class="px-4 py-3">Generated Images</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($dateWise as $row)
                            <tr class="border-t">
                                <td class="px-4 py-3 font-semibold">
                                    {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $row->total_uploaded }}
                                </td>

                                <td class="px-4 py-3 text-green-600 font-semibold">
                                    {{ $row->total_generated }}
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.usage.date', [$user->id, $row->date]) }}"
                                       class="rounded-lg bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-700">
                                        View Images
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No images found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts::app>