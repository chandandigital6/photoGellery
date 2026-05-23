<x-layouts::app :title="__('Image Tool')">

<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

    @if (session('success'))
        <div
            class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- PAGE HEADING --}}
    <div>
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">
            Image Tool Dashboard
        </h1>

        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
            Upload images, generate logo overlay images and track your plan usage.
        </p>
    </div>

   {{-- PLAN SUMMARY --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <div
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            Plan Type
        </p>

        <h3 class="mt-2 text-2xl font-bold uppercase text-blue-600">
            {{ $usageSummary['limit_type'] }}
        </h3>
    </div>

    <div
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            Total Limit
        </p>

        <h3 class="mt-2 text-2xl font-bold text-neutral-900 dark:text-white">
            {{ $usageSummary['limit_mb'] }} MB
        </h3>
    </div>

    <div
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            Used Storage
        </p>

        <h3 class="mt-2 text-2xl font-bold text-orange-600">
            {{ $usageSummary['used_mb'] }} MB
        </h3>
    </div>

    <div
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            Remaining
        </p>

        <h3 class="mt-2 text-2xl font-bold text-green-600">
            {{ $usageSummary['remaining_mb'] }} MB
        </h3>
    </div>

    <div
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            Generated Today
        </p>

        <h3 class="mt-2 text-2xl font-bold text-purple-600">
            {{ $usageSummary['generated_today_count'] }}/{{ $usageSummary['uploaded_today_count'] }}
        </h3>
    </div>

</div>
    {{-- DATE WISE REPORT --}}
    <div
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">
                    Date Wise Report
                </h2>

                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Uploaded and generated image report date-wise.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-100 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Uploaded</th>
                        <th class="px-4 py-3">Generated</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($dateWiseStats as $row)
                        <tr class="border-t dark:border-neutral-700">

                            <td class="px-4 py-3 font-semibold">
                                {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $row->total_uploaded }}
                            </td>

                            <td class="px-4 py-3 font-bold text-green-600">
                                {{ $row->total_generated }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('image.tool', ['date' => $row->date]) }}"
                                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                    View Images
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                No report found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MULTIPLE IMAGE UPLOAD --}}
    <div
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

        <div class="mb-5">
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">
                Multiple Image Upload
            </h2>

            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                Upload multiple images date-wise.
            </p>
        </div>

        <input type="date" id="upload_date"
            class="mb-4 rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
            value="{{ $selectedDate ?? date('Y-m-d') }}">

        <form action="{{ route('image.tool.upload') }}"
            class="dropzone rounded-2xl border-2 border-dashed border-neutral-300 bg-neutral-50 p-8 dark:border-neutral-700 dark:bg-neutral-800"
            id="imageDropzone">
            @csrf
        </form>
    </div>

    {{-- DATE FILTER --}}
    <form method="GET" action="{{ route('image.tool') }}"
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

        <h2 class="mb-4 text-2xl font-bold text-neutral-900 dark:text-white">
            Select Date Images
        </h2>

        <div class="grid gap-4 md:grid-cols-3">

            <div>
                <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    Select Date
                </label>

                <input type="date"
                    name="date"
                    value="{{ $selectedDate ?? date('Y-m-d') }}"
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

    {{-- GENERATE IMAGES --}}
    <form method="POST"
        action="{{ route('image.tool.generate') }}"
        enctype="multipart/form-data"
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

        @csrf

        <input type="hidden"
            name="generate_date"
            value="{{ $selectedDate ?? date('Y-m-d') }}">

        <h2 class="mb-4 text-2xl font-bold text-neutral-900 dark:text-white">
            Generate With Logo
        </h2>

        <div class="grid gap-4 md:grid-cols-3">

            <div>
                <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    Upload Logo
                </label>

                <input type="file"
                    name="logo"
                    required
                    accept="image/*"
                    class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    Logo Position
                </label>

                <select name="position"
                    required
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

                <input type="number"
                    name="logo_size"
                    value="18"
                    min="5"
                    max="60"
                    required
                    class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    Logo Opacity
                </label>

                <select name="opacity"
                    required
                    class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">

                    <option value="100">100% - Full Clear</option>
                    <option value="90">90%</option>
                    <option value="80">80%</option>
                    <option value="70">70%</option>
                    <option value="60">60%</option>
                    <option value="50">50%</option>
                    <option value="40">40%</option>
                    <option value="30">30%</option>

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

    {{-- IMAGES --}}
    <div
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">
                    Images - {{ $selectedDate ?? date('Y-m-d') }}
                </h2>

                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Uploaded and generated images.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">

            @forelse($images as $img)

                <div
                    class="rounded-2xl border border-neutral-200 bg-white p-3 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">

                    {{-- ORIGINAL IMAGE --}}
                    <img src="{{ asset('storage/' . $img->original_image) }}"
                        class="h-40 w-full rounded-xl object-cover">

                    <div class="mt-2 text-xs text-neutral-500">
                        Original Image
                    </div>

                    {{-- GENERATED IMAGE --}}
                    @if ($img->generated_image)

                        <div class="mt-4">

                            <p class="mb-2 text-xs font-semibold text-green-600">
                                Generated Image
                            </p>

                            <img src="{{ asset('storage/' . $img->generated_image) }}"
                                class="h-40 w-full rounded-xl object-cover">

                            <a href="{{ asset('storage/' . $img->generated_image) }}"
                                download
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

<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">

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
                    formData.append(
                        'upload_date',
                        document.getElementById('upload_date').value
                    );
                },

                success: function(file, response) {

                    window.location.href =
                        "{{ route('image.tool') }}?date=" +
                        document.getElementById('upload_date').value;
                },

                error: function(file, response) {

                    console.log(response);

                    let message = 'Image upload failed.';

                    if (typeof response === 'string') {
                        message = response;
                    }

                    if (response && response.message) {
                        message = response.message;
                    }

                    alert(message);
                }
            });
        }
    });

</script>


</x-layouts::app>
