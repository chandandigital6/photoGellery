<x-layouts::app :title="__('User Usage')">

    <div class="p-6 space-y-6">
        <div>
            <h1 class="text-2xl font-bold">User Usage Dashboard</h1>
            <p class="text-sm text-gray-500">
                User-wise plan limit, used MB, remaining MB and generated images.
            </p>
        </div>

        <div class="overflow-x-auto rounded-2xl border bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Plan</th>
                        <th class="px-4 py-3">Limit</th>
                        <th class="px-4 py-3">Used</th>
                        <th class="px-4 py-3">Remaining</th>
                        <th class="px-4 py-3">Uploaded</th>
                        <th class="px-4 py-3">Generated</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr class="border-t">
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </td>

                            <td class="px-4 py-3 uppercase">
                                {{ $user->plan_type }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $user->limit_mb }} MB
                            </td>

                            <td class="px-4 py-3 text-orange-600 font-semibold">
                                {{ $user->used_mb }} MB
                            </td>

                            <td class="px-4 py-3 text-green-600 font-semibold">
                                {{ $user->remaining_mb }} MB
                            </td>

                            <td class="px-4 py-3">
                                {{ $user->total_uploaded_images }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $user->total_generated_images }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.usage.show', $user->id) }}"
                                   class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts::app>