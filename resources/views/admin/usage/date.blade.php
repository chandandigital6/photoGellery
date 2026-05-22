<x-layouts::app :title="__('Generated Images')">

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Generated Images</h1>
                <p class="text-sm text-gray-500">
                    {{ $user->name }} — {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                </p>
            </div>

            <a href="{{ route('admin.usage.show', $user->id) }}"
               class="rounded-lg border px-4 py-2 text-sm">
                Back
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            @forelse($images as $img)
                <div class="rounded-2xl border bg-white p-3 shadow-sm">
                    <img src="{{ asset('storage/' . $img->original_image) }}"
                         class="h-40 w-full rounded-xl object-cover">

                    <p class="mt-2 text-xs text-gray-500">Original Image</p>

                    @if($img->generated_image)
                        <div class="mt-4">
                            <p class="mb-2 text-xs font-semibold text-green-600">
                                Generated Image
                            </p>

                            <img src="{{ asset('storage/' . $img->generated_image) }}"
                                 class="h-40 w-full rounded-xl object-cover">

                            <a href="{{ asset('storage/' . $img->generated_image) }}"
                               download
                               class="mt-3 inline-flex w-full justify-center rounded-xl bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-700">
                                Download
                            </a>
                        </div>
                    @else
                        <p class="mt-3 text-xs font-semibold text-orange-500">
                            Not Generated
                        </p>
                    @endif
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed p-10 text-center text-gray-500">
                    No images found.
                </div>
            @endforelse
        </div>
    </div>

</x-layouts::app>