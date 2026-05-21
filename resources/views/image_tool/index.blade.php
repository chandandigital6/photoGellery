<x-layouts::app :title="__('Image Tool')">

    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        @if (session('success'))
            <div
                class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
                Image Tool
            </h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                Upload images date-wise and generate logo overlay images for selected date.
            </p>
        </div>

        <div
            class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="mb-4 text-xl font-bold text-neutral-900 dark:text-white">
                Multiple Image Upload
            </h2>

            <input type="date" id="upload_date"
                class="mb-4 rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                value="{{ $selectedDate ?? date('Y-m-d') }}">

            <form action="{{ route('image.tool.upload') }}"
                class="dropzone rounded-2xl border-2 border-dashed border-neutral-300 bg-neutral-50 p-8 dark:border-neutral-700 dark:bg-neutral-800"
                id="imageDropzone">
                @csrf
            </form>
        </div>

        <form method="GET" action="{{ route('image.tool') }}"
            class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

            <h2 class="mb-4 text-xl font-bold text-neutral-900 dark:text-white">
                Select Date Images
            </h2>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Select Date
                    </label>
                    <input type="date" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full rounded-xl bg-neutral-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-neutral-700 dark:bg-white dark:text-black">
                        Show Images
                    </button>
                </div>
            </div>
        </form>

        <form method="POST" action="{{ route('image.tool.generate') }}" enctype="multipart/form-data"
            class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            @csrf

            <input type="hidden" name="generate_date" value="{{ $selectedDate ?? date('Y-m-d') }}">

            <h2 class="mb-4 text-xl font-bold text-neutral-900 dark:text-white">
                Generate With Logo
            </h2>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Upload Logo
                    </label>
                    <input type="file" name="logo" required accept="image/*"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Logo Position
                    </label>
                    <select name="position" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">Select Logo Position</option>
                        <option value="top-left">Top Left</option>
                        <option value="top-right">Top Right</option>
                        <option value="bottom-left">Bottom Left</option>
                        <option value="bottom-right">Bottom Right</option>
                    </select>
                </div>

                           <div>
    <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
        Logo Size (%)
    </label>
    <input type="number" name="logo_size" value="18" min="5" max="60" required
        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
</div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Logo Opacity
                    </label>

                    <select name="opacity" required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="100">100% - Full Clear</option>
                        <option value="90">90% - हल्का</option>
                        <option value="80">80%</option>
                        <option value="70">70%</option>
                        <option value="60">60%</option>
                        <option value="50">50% - Medium</option>
                        <option value="40">40%</option>
                        <option value="30">30% - Light</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full rounded-xl bg-black px-5 py-3 text-sm font-semibold text-white transition hover:bg-neutral-800 dark:bg-white dark:text-black">
                        Generate All Images
                    </button>
                </div>
            </div>
        </form>

        <div
            class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="mb-4 text-xl font-bold text-neutral-900 dark:text-white">
                Images - {{ $selectedDate ?? date('Y-m-d') }}
            </h2>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @forelse($images as $img)
                    <div
                        class="rounded-2xl border border-neutral-200 bg-white p-3 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">

                        <img src="{{ asset('storage/' . $img->original_image) }}"
                            class="h-40 w-full rounded-xl object-cover">

                        <div class="mt-2 text-xs text-neutral-500">
                            Original Image
                        </div>

                        @if ($img->generated_image)
                            <div class="mt-4">
                                <p class="mb-2 text-xs font-semibold text-green-600">
                                    Generated Image
                                </p>

                                <img src="{{ asset('storage/' . $img->generated_image) }}"
                                    class="h-40 w-full rounded-xl object-cover">

                                <a href="{{ asset('storage/' . $img->generated_image) }}" download
                                    class="mt-3 inline-flex w-full items-center justify-center rounded-xl bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-700">
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
                    <div
                        class="col-span-full rounded-2xl border border-dashed border-neutral-300 p-10 text-center text-neutral-500 dark:border-neutral-700">
                        No images found for this date.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

    <script>
        Dropzone.autoDiscover = false;

        document.addEventListener('DOMContentLoaded', function() {
            const dropzoneElement = document.getElementById('imageDropzone');

            if (dropzoneElement && !dropzoneElement.dropzone) {
                new Dropzone('#imageDropzone', {
                    paramName: 'file',
                    maxFilesize: 10,
                    acceptedFiles: 'image/*',
                    uploadMultiple: false,
                    parallelUploads: 10,
                    addRemoveLinks: true,

                    sending: function(file, xhr, formData) {
                        formData.append('upload_date', document.getElementById('upload_date').value);
                    },

                    success: function(file, response) {
                        window.location.href = "{{ route('image.tool') }}?date=" + document
                            .getElementById('upload_date').value;
                    },

                    error: function(file, response) {
                        console.log(response);
                        alert('Image upload failed.');
                    }
                });
            }
        });
    </script>

</x-layouts::app>
