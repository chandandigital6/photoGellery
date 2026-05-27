<x-layouts::app :title="__('Image Tool')">

<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- PAGE HEADING --}}
    <div>
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">
            Image Tool Dashboard
        </h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
            Upload images, preview logo setting, then generate all images.
        </p>
    </div>

    {{-- PLAN SUMMARY --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Plan Type</p>
            <h3 class="mt-2 text-2xl font-bold uppercase text-blue-600">
                {{ $usageSummary['limit_type'] }}
            </h3>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Total Limit</p>
            <h3 class="mt-2 text-2xl font-bold text-neutral-900 dark:text-white">
                {{ $usageSummary['limit_mb'] }} MB
            </h3>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Used Storage</p>
            <h3 class="mt-2 text-2xl font-bold text-orange-600">
                {{ $usageSummary['used_mb'] }} MB
            </h3>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Remaining</p>
            <h3 class="mt-2 text-2xl font-bold text-green-600">
                {{ $usageSummary['remaining_mb'] }} MB
            </h3>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Generated Today</p>
            <h3 class="mt-2 text-2xl font-bold text-purple-600">
                {{ $usageSummary['generated_today_count'] }}/{{ $usageSummary['uploaded_today_count'] }}
            </h3>
        </div>

    </div>

    {{-- DATE WISE REPORT --}}
    <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="mb-5">
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">
                Date Wise Report
            </h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                Uploaded and generated image report date-wise.
            </p>
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
                            <td colspan="4" class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                No report found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MULTIPLE IMAGE UPLOAD --}}
    <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="mb-5">
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">
                Multiple Image Upload
            </h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                Multiple images upload karo. Sab upload hone ke baad page auto reload hoga.
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
    {{-- <form method="GET" action="{{ route('image.tool') }}"
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
    </form> --}}

    {{-- GENERATE WITH LIVE PREVIEW --}}
    <form method="POST"
        action="{{ route('image.tool.generate') }}"
        enctype="multipart/form-data"
        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

        @csrf

        <input type="hidden" name="generate_date" value="{{ $selectedDate ?? date('Y-m-d') }}">

        <h2 class="mb-4 text-2xl font-bold text-neutral-900 dark:text-white">
            Generate With Logo
        </h2>

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- LEFT PREVIEW --}}
            <div>
                <div class="relative flex min-h-[360px] items-center justify-center overflow-hidden rounded-2xl border border-neutral-300 bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-800">

                    @if(count($images))
                        <img id="previewMainImage"
                            src="{{ asset('storage/' . $images[0]->original_image) }}"
                            class="max-h-[520px] w-full rounded-2xl object-contain">
                    @else
                        <div class="flex h-80 items-center justify-center text-neutral-500">
                            No image found. Upload image first.
                        </div>
                    @endif

                    <img id="previewLogo"
                        src=""
                        class="absolute hidden"
                        style="width:18%; opacity:1; top:30px; right:30px;">
                </div>

                {{-- IMAGE PREVIEW SLIDER --}}
                @if(count($images))
                    <p class="mt-3 text-xs text-neutral-500">
                        Slider se image select karke preview dekho.
                    </p>

                    <div class="relative mt-4">
                        <button type="button"
                            id="thumbPrev"
                            class="absolute left-0 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/70 px-3 py-2 text-white shadow hover:bg-black">
                            ‹
                        </button>

                        <div id="thumbSlider"
                            class="flex gap-3 overflow-x-auto scroll-smooth px-10 pb-2"
                            style="scrollbar-width:none; -ms-overflow-style:none;">

                            @foreach($images as $key => $img)
                                <button type="button"
                                    class="preview-thumb h-24 min-w-[140px] overflow-hidden rounded-xl border-2 bg-neutral-100 transition {{ $key === 0 ? 'border-blue-600' : 'border-transparent' }}"
                                    data-src="{{ asset('storage/' . $img->original_image) }}">

                                    <img src="{{ asset('storage/' . $img->original_image) }}"
                                        class="h-full w-full object-cover">
                                </button>
                            @endforeach

                        </div>

                        <button type="button"
                            id="thumbNext"
                            class="absolute right-0 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/70 px-3 py-2 text-white shadow hover:bg-black">
                            ›
                        </button>
                    </div>

                    <style>
                        #thumbSlider::-webkit-scrollbar {
                            display: none;
                        }
                    </style>
                @endif
            </div>

            {{-- RIGHT CONTROLS --}}
            <div class="space-y-4">

                <div>
                    <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Upload Logo
                    </label>

                    <input type="file"
                        name="logo"
                        id="logoInput"
                        required
                        accept="image/*"
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Logo Position
                    </label>

                    <select name="position"
                        id="logoPosition"
                        required
                        class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">

                        <option value="top-left">Top Left</option>
                        <option value="top-right" selected>Top Right</option>
                        <option value="bottom-left">Bottom Left</option>
                        <option value="bottom-right">Bottom Right</option>
                    </select>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <span>Logo Size</span>
                        <span><span id="logoSizeText">18</span>%</span>
                    </div>

                    <input type="range" id="logoSizeRange" min="5" max="60" value="18" class="w-full">
                    <input type="hidden" name="logo_size" id="logoSizeInput" value="18">
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        <span>Logo Opacity</span>
                        <span><span id="logoOpacityText">100</span>%</span>
                    </div>

                    <input type="range" id="logoOpacityRange" min="30" max="100" value="100" class="w-full">
                    <input type="hidden" name="opacity" id="logoOpacityInput" value="100">
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-black px-5 py-3 text-sm font-semibold text-white transition hover:bg-neutral-800 dark:bg-white dark:text-black">
                    Generate All Images
                </button>

                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                    Preview sirf selected sample image par dikhega. Generate karne par same logo setting selected date ki sabhi non-generated images par apply hogi.
                </p>
            </div>

        </div>
    </form>

    {{-- IMAGES --}}
    <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">

        <div class="mb-5">
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">
                Images - {{ $selectedDate ?? date('Y-m-d') }}
            </h2>

            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                Original images and generated preview/download.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">

            @forelse($images as $img)
                <div class="rounded-2xl border border-neutral-200 bg-white p-3 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">

                    <img src="{{ asset('storage/' . $img->original_image) }}"
                        class="h-40 w-full rounded-xl object-cover">

                    <div class="mt-2 text-xs text-neutral-500">
                        Original Image
                    </div>

                    @if ($img->generated_image)
                        <button type="button"
                            class="preview-generated-btn mt-3 inline-flex w-full items-center justify-center rounded-xl bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-700"
                            data-image="{{ asset('storage/' . $img->generated_image) }}">
                            Preview / Download
                        </button>
                    @else
                        <p class="mt-3 text-xs font-semibold text-orange-500">
                            Not Generated
                        </p>
                    @endif

                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-neutral-300 p-10 text-center text-neutral-500 dark:border-neutral-700">
                    No images found for this date.
                </div>
            @endforelse

        </div>
    </div>

</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
Dropzone.autoDiscover = false;

document.addEventListener('DOMContentLoaded', function () {

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
                file.previewElement.classList.add('dz-success');
            },

            queuecomplete: function() {
                window.location.href =
                    "{{ route('image.tool') }}?date=" +
                    document.getElementById('upload_date').value;
            },

            error: function(file, response) {
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

    const previewMainImage = document.getElementById('previewMainImage');
    const previewLogo = document.getElementById('previewLogo');
    const logoInput = document.getElementById('logoInput');
    const logoPosition = document.getElementById('logoPosition');

    const logoSizeRange = document.getElementById('logoSizeRange');
    const logoSizeInput = document.getElementById('logoSizeInput');
    const logoSizeText = document.getElementById('logoSizeText');

    const logoOpacityRange = document.getElementById('logoOpacityRange');
    const logoOpacityInput = document.getElementById('logoOpacityInput');
    const logoOpacityText = document.getElementById('logoOpacityText');

    document.querySelectorAll('.preview-thumb').forEach(function(button) {
        button.addEventListener('click', function() {
            if (previewMainImage) {
                previewMainImage.src = this.dataset.src;
            }

            document.querySelectorAll('.preview-thumb').forEach(function(btn) {
                btn.classList.remove('border-blue-600');
                btn.classList.add('border-transparent');
            });

            this.classList.remove('border-transparent');
            this.classList.add('border-blue-600');
        });
    });

    const thumbSlider = document.getElementById('thumbSlider');
    const thumbPrev = document.getElementById('thumbPrev');
    const thumbNext = document.getElementById('thumbNext');

    if (thumbSlider && thumbPrev && thumbNext) {
        thumbPrev.addEventListener('click', function () {
            thumbSlider.scrollBy({
                left: -300,
                behavior: 'smooth'
            });
        });

        thumbNext.addEventListener('click', function () {
            thumbSlider.scrollBy({
                left: 300,
                behavior: 'smooth'
            });
        });
    }

    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file && previewLogo) {
                previewLogo.src = URL.createObjectURL(file);
                previewLogo.classList.remove('hidden');
            }
        });
    }

    function applyLogoPosition() {
        if (!previewLogo || !logoPosition) return;

        previewLogo.style.top = '';
        previewLogo.style.bottom = '';
        previewLogo.style.left = '';
        previewLogo.style.right = '';

        const margin = '30px';

        switch (logoPosition.value) {
            case 'top-left':
                previewLogo.style.top = margin;
                previewLogo.style.left = margin;
                break;

            case 'top-right':
                previewLogo.style.top = margin;
                previewLogo.style.right = margin;
                break;

            case 'bottom-left':
                previewLogo.style.bottom = margin;
                previewLogo.style.left = margin;
                break;

            case 'bottom-right':
                previewLogo.style.bottom = margin;
                previewLogo.style.right = margin;
                break;
        }
    }

    if (logoPosition) {
        logoPosition.addEventListener('change', applyLogoPosition);
        applyLogoPosition();
    }

    if (logoSizeRange) {
        logoSizeRange.addEventListener('input', function() {
            const value = this.value;

            logoSizeInput.value = value;
            logoSizeText.innerText = value;

            if (previewLogo) {
                previewLogo.style.width = value + '%';
            }
        });
    }

    if (logoOpacityRange) {
        logoOpacityRange.addEventListener('input', function() {
            const value = this.value;

            logoOpacityInput.value = value;
            logoOpacityText.innerText = value;

            if (previewLogo) {
                previewLogo.style.opacity = value / 100;
            }
        });
    }

    document.querySelectorAll('.preview-generated-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const imageUrl = this.dataset.image;

            Swal.fire({
                title: 'Generated Image Preview',
                html: `
                    <div style="text-align:center;">
                        <img src="${imageUrl}" style="max-width:100%; max-height:70vh; object-fit:contain; border-radius:14px; margin-bottom:15px;">
                        <br>
                        <a href="${imageUrl}" download
                           style="display:inline-block;background:#16a34a;color:#fff;padding:10px 18px;border-radius:10px;font-weight:600;text-decoration:none;">
                            Download Image
                        </a>
                    </div>
                `,
                width: 700,
                showConfirmButton: false,
                showCloseButton: true
            });
        });
    });

});
</script>

</x-layouts::app>

