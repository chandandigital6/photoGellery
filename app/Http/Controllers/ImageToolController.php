<?php
namespace App\Http\Controllers;

use App\Models\UploadedImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ImageToolController extends Controller
{



public function index(Request $request)
{
    $selectedDate = $request->get('date', date('Y-m-d'));
    $user = auth()->user();

    $plan = \App\Models\UserPlan::where('user_id', $user->id)
        ->where('is_active', true)
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->latest()
        ->first();

    $images = UploadedImage::where('user_id', $user->id)
        ->whereDate('upload_date', $selectedDate)
        ->latest()
        ->get();

    $dateWiseStats = UploadedImage::where('user_id', $user->id)
        ->selectRaw('DATE(upload_date) as date')
        ->selectRaw('COUNT(*) as total_uploaded')
        ->selectRaw('SUM(CASE WHEN generated_image IS NOT NULL THEN 1 ELSE 0 END) as total_generated')
        ->groupByRaw('DATE(upload_date)')
        ->orderByDesc('date')
        ->get();

    $usedBytesQuery = \App\Models\UserUsage::where('user_id', $user->id);

    if ($plan) {
        if ($plan->limit_type === 'daily') {
            $usedBytesQuery->whereDate('usage_date', today());
        }

        if ($plan->limit_type === 'monthly') {
            $usedBytesQuery->whereYear('usage_date', now()->year)
                ->whereMonth('usage_date', now()->month);
        }

        if ($plan->limit_type === 'yearly') {
            $usedBytesQuery->whereYear('usage_date', now()->year);
        }
    }

    $usedBytes = $usedBytesQuery->sum('used_bytes');
    $limitBytes = $plan ? $plan->limit_mb * 1024 * 1024 : 0;

    $usageSummary = [
        'plan' => $plan,
        'limit_mb' => $plan?->limit_mb ?? 0,
        'limit_type' => $plan?->limit_type ?? 'No Plan',
        'used_mb' => round($usedBytes / 1024 / 1024, 2),
        'remaining_mb' => $plan ? max(0, round(($limitBytes - $usedBytes) / 1024 / 1024, 2)) : 0,
        'uploaded_today_count' => $images->count(),
        'generated_today_count' => $images->whereNotNull('generated_image')->count(),
    ];

    return view('image_tool.index', compact(
        'images',
        'selectedDate',
        'usageSummary',
        'dateWiseStats'
    ));
}

public function upload(Request $request)
{
    $request->validate([
        'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
        'upload_date' => ['required', 'date'],
    ]);

    $user = auth()->user();

    $plan = \App\Models\UserPlan::where('user_id', $user->id)
        ->where('is_active', true)
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->latest()
        ->first();

    if (!$plan) {
        return response()->json([
            'success' => false,
            'message' => 'Aapka koi active plan nahi hai.',
        ], 403);
    }

    $fileSize = $request->file('file')->getSize();
    $limitBytes = $plan->limit_mb * 1024 * 1024;

    $query = \App\Models\UserUsage::where('user_id', $user->id);

    if ($plan->limit_type === 'daily') {
        $query->whereDate('usage_date', today());
    }

    if ($plan->limit_type === 'monthly') {
        $query->whereYear('usage_date', now()->year)
              ->whereMonth('usage_date', now()->month);
    }

    if ($plan->limit_type === 'yearly') {
        $query->whereYear('usage_date', now()->year);
    }

    $usedBytes = $query->sum('used_bytes');

    if (($usedBytes + $fileSize) > $limitBytes) {
        return response()->json([
            'success' => false,
            'message' => 'Upload limit complete ho chuki hai. Aapka plan limit: '.$plan->limit_mb.' MB '.$plan->limit_type,
        ], 403);
    }

    $date = $request->upload_date;
    $folder = 'uploads/images/' . $date;

    $path = $request->file('file')->store($folder, 'public');

    \App\Models\UploadedImage::create([
        'user_id' => $user->id,
        'upload_date' => $date,
        'original_image' => $path,
    ]);

    \App\Models\UserUsage::create([
        'user_id' => $user->id,
        'usage_date' => today(),
        'used_bytes' => $fileSize,
    ]);

    return response()->json([
        'success' => true,
        'path' => asset('storage/' . $path),
    ]);
}



public function generate(Request $request)
{
    ini_set('memory_limit', '512M');

    $request->validate([
        'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        'position' => ['required', 'in:top-left,top-right,bottom-left,bottom-right'],
        'generate_date' => ['required', 'date'],
        'logo_size' => ['required', 'integer', 'min:5', 'max:60'],
        'opacity' => ['required', 'integer', 'min:30', 'max:100'],
    ]);

    $generateDate = $request->generate_date;
    $logoSizePercent = (int) $request->logo_size;
    $opacityPercent = (int) $request->opacity;

    // $images = UploadedImage::whereDate('upload_date', $generateDate)->get();
    // $images = UploadedImage::where('user_id', auth()->id())
    // ->whereDate('upload_date', $generateDate)
    // ->get();

    $images = UploadedImage::where('user_id', auth()->id())
    ->whereDate('upload_date', $generateDate)
    ->whereNull('generated_image') // sirf non-generated images
    ->get();

   if ($images->isEmpty()) {
    return back()->with('success', 'Is date ki sabhi images already generated hain.');
}

    $logoPath = $request->file('logo')->store('uploads/logos/' . $generateDate, 'public');
    $logoFullPath = storage_path('app/public/' . $logoPath);

    if (!file_exists($logoFullPath)) {
        return back()->with('success', 'Logo file not found.');
    }

    $logoSource = @imagecreatefromstring(file_get_contents($logoFullPath));

    if (!$logoSource) {
        return back()->with('success', 'Logo image invalid hai.');
    }

    foreach ($images as $record) {
        $mainPath = storage_path('app/public/' . $record->original_image);

        if (!file_exists($mainPath)) {
            continue;
        }

        $image = @imagecreatefromstring(file_get_contents($mainPath));

        if (!$image) {
            continue;
        }

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $logoWidth = imagesx($logoSource);
        $logoHeight = imagesy($logoSource);

        $newLogoWidth = intval($imageWidth * ($logoSizePercent / 100));
        $newLogoHeight = intval(($logoHeight / $logoWidth) * $newLogoWidth);

        $resizedLogo = imagecreatetruecolor($newLogoWidth, $newLogoHeight);

        imagealphablending($resizedLogo, false);
        imagesavealpha($resizedLogo, true);

        imagecopyresampled(
            $resizedLogo,
            $logoSource,
            0,
            0,
            0,
            0,
            $newLogoWidth,
            $newLogoHeight,
            $logoWidth,
            $logoHeight
        );

        // ✅ Opacity apply
        $opacity = max(0, min(100, $opacityPercent));

        for ($xPixel = 0; $xPixel < $newLogoWidth; $xPixel++) {
            for ($yPixel = 0; $yPixel < $newLogoHeight; $yPixel++) {
                $rgba = imagecolorat($resizedLogo, $xPixel, $yPixel);

                $alpha = ($rgba & 0x7F000000) >> 24;
                $red = ($rgba >> 16) & 0xFF;
                $green = ($rgba >> 8) & 0xFF;
                $blue = $rgba & 0xFF;

                $newAlpha = 127 - ((127 - $alpha) * ($opacity / 100));
                $newAlpha = (int) max(0, min(127, $newAlpha));

                $color = imagecolorallocatealpha($resizedLogo, $red, $green, $blue, $newAlpha);
                imagesetpixel($resizedLogo, $xPixel, $yPixel, $color);
            }
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $margin = 30;

        $x = match ($request->position) {
            'top-left', 'bottom-left' => $margin,
            'top-right', 'bottom-right' => $imageWidth - $newLogoWidth - $margin,
        };

        $y = match ($request->position) {
            'top-left', 'top-right' => $margin,
            'bottom-left', 'bottom-right' => $imageHeight - $newLogoHeight - $margin,
        };

        imagecopy($image, $resizedLogo, $x, $y, 0, 0, $newLogoWidth, $newLogoHeight);

        $generatedFolder = 'uploads/generated/' . $generateDate;
        Storage::disk('public')->makeDirectory($generatedFolder);

        $generatedName = $generatedFolder . '/generated_' . uniqid() . '_' . $record->id . '.jpg';
        $savePath = storage_path('app/public/' . $generatedName);

        imagejpeg($image, $savePath, 85);

        imagedestroy($image);
        imagedestroy($resizedLogo);

        $record->update([
            'logo_image' => $logoPath,
            'position' => $request->position,
            'generated_image' => $generatedName,
        ]);

        gc_collect_cycles();
    }

    imagedestroy($logoSource);

    return redirect()
        ->route('image.tool', ['date' => $generateDate])
        ->with('success', 'Selected date ke saare images generate ho gaye.');
}





  public function uploadold(Request $request)
{
    $request->validate([
        'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
        'upload_date' => ['required', 'date'],
    ]);

    $date = $request->upload_date;
    $folder = 'uploads/images/' . $date;

    $path = $request->file('file')->store($folder, 'public');

    UploadedImage::create([
        'upload_date' => $date,
        'original_image' => $path,
    ]);

    return response()->json([
        'success' => true,
        'path' => asset('storage/' . $path),
    ]);
}





public function generatelogosize(Request $request)
{
    ini_set('memory_limit', '512M');

    $request->validate([
        'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        'position' => ['required', 'in:top-left,top-right,bottom-left,bottom-right'],
        'generate_date' => ['required', 'date'],
        'logo_size' => ['required', 'integer', 'min:5', 'max:60'],
    ]);

    $generateDate = $request->generate_date;
    $logoSizePercent = (int) $request->logo_size;

    $images = UploadedImage::whereDate('upload_date', $generateDate)->get();

    if ($images->isEmpty()) {
        return back()->with('success', 'Is date par koi image nahi mili.');
    }

    $logoPath = $request->file('logo')->store('uploads/logos/' . $generateDate, 'public');
    $logoFullPath = storage_path('app/public/' . $logoPath);

    if (!file_exists($logoFullPath)) {
        return back()->with('success', 'Logo file not found.');
    }

    $logoSource = @imagecreatefromstring(file_get_contents($logoFullPath));

    if (!$logoSource) {
        return back()->with('success', 'Logo image invalid hai.');
    }

    foreach ($images as $record) {
        $mainPath = storage_path('app/public/' . $record->original_image);

        if (!file_exists($mainPath)) {
            continue;
        }

        $image = @imagecreatefromstring(file_get_contents($mainPath));

        if (!$image) {
            continue;
        }

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $logoWidth = imagesx($logoSource);
        $logoHeight = imagesy($logoSource);

        // Logo size percentage ke according
        $newLogoWidth = intval($imageWidth * ($logoSizePercent / 100));
        $newLogoHeight = intval(($logoHeight / $logoWidth) * $newLogoWidth);

        $resizedLogo = imagecreatetruecolor($newLogoWidth, $newLogoHeight);

        imagealphablending($resizedLogo, false);
        imagesavealpha($resizedLogo, true);

        imagecopyresampled(
            $resizedLogo,
            $logoSource,
            0,
            0,
            0,
            0,
            $newLogoWidth,
            $newLogoHeight,
            $logoWidth,
            $logoHeight
        );

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $margin = 30;

        $x = match ($request->position) {
            'top-left', 'bottom-left' => $margin,
            'top-right', 'bottom-right' => $imageWidth - $newLogoWidth - $margin,
        };

        $y = match ($request->position) {
            'top-left', 'top-right' => $margin,
            'bottom-left', 'bottom-right' => $imageHeight - $newLogoHeight - $margin,
        };

        imagecopy($image, $resizedLogo, $x, $y, 0, 0, $newLogoWidth, $newLogoHeight);

        $generatedFolder = 'uploads/generated/' . $generateDate;
        Storage::disk('public')->makeDirectory($generatedFolder);

        $generatedName = $generatedFolder . '/generated_' . uniqid() . '_' . $record->id . '.jpg';
        $savePath = storage_path('app/public/' . $generatedName);

        imagejpeg($image, $savePath, 85);

        imagedestroy($image);
        imagedestroy($resizedLogo);

        $record->update([
            'logo_image' => $logoPath,
            'position' => $request->position,
            'generated_image' => $generatedName,
        ]);

        gc_collect_cycles();
    }

    imagedestroy($logoSource);

    return redirect()
        ->route('image.tool', ['date' => $generateDate])
        ->with('success', 'Selected date ke saare images generate ho gaye.');
}


public function generateold(Request $request)
{
    ini_set('memory_limit', '512M');

    $request->validate([
        'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        'position' => ['required', 'in:top-left,top-right,bottom-left,bottom-right'],
        'generate_date' => ['required', 'date'],
    ]);

    $generateDate = $request->generate_date;

    $images = UploadedImage::whereDate('upload_date', $generateDate)->get();

    if ($images->isEmpty()) {
        return back()->with('success', 'Is date par koi image nahi mili.');
    }

    $logoPath = $request->file('logo')->store('uploads/logos/' . $generateDate, 'public');
    $logoFullPath = storage_path('app/public/' . $logoPath);

    if (!file_exists($logoFullPath)) {
        return back()->with('success', 'Logo file not found.');
    }

    // ✅ Logo sirf ek baar load hoga
    $logoSource = @imagecreatefromstring(file_get_contents($logoFullPath));

    if (!$logoSource) {
        return back()->with('success', 'Logo image invalid hai.');
    }

    foreach ($images as $record) {
        $mainPath = storage_path('app/public/' . $record->original_image);

        if (!file_exists($mainPath)) {
            continue;
        }

        $image = @imagecreatefromstring(file_get_contents($mainPath));

        if (!$image) {
            continue;
        }

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $logoWidth = imagesx($logoSource);
        $logoHeight = imagesy($logoSource);

        $newLogoWidth = intval($imageWidth * 0.18);
        $newLogoHeight = intval(($logoHeight / $logoWidth) * $newLogoWidth);

        $resizedLogo = imagecreatetruecolor($newLogoWidth, $newLogoHeight);

        imagealphablending($resizedLogo, false);
        imagesavealpha($resizedLogo, true);

        imagecopyresampled(
            $resizedLogo,
            $logoSource,
            0,
            0,
            0,
            0,
            $newLogoWidth,
            $newLogoHeight,
            $logoWidth,
            $logoHeight
        );

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $margin = 30;

        $x = match ($request->position) {
            'top-left', 'bottom-left' => $margin,
            'top-right', 'bottom-right' => $imageWidth - $newLogoWidth - $margin,
        };

        $y = match ($request->position) {
            'top-left', 'top-right' => $margin,
            'bottom-left', 'bottom-right' => $imageHeight - $newLogoHeight - $margin,
        };

        imagecopy($image, $resizedLogo, $x, $y, 0, 0, $newLogoWidth, $newLogoHeight);

        $generatedFolder = 'uploads/generated/' . $generateDate;
        Storage::disk('public')->makeDirectory($generatedFolder);

        $generatedName = $generatedFolder . '/generated_' . uniqid() . '_' . $record->id . '.jpg';
        $savePath = storage_path('app/public/' . $generatedName);

        imagejpeg($image, $savePath, 85);

        imagedestroy($image);
        imagedestroy($resizedLogo);

        $record->update([
            'logo_image' => $logoPath,
            'position' => $request->position,
            'generated_image' => $generatedName,
        ]);

        gc_collect_cycles();
    }

    imagedestroy($logoSource);

    return redirect()
        ->route('image.tool', ['date' => $generateDate])
        ->with('success', 'Selected date ke saare images generate ho gaye.');
}




public function generateOpacity(Request $request)
{
    ini_set('memory_limit', '512M');

    $request->validate([
        'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        'position' => ['required', 'in:top-left,top-right,bottom-left,bottom-right'],
        'generate_date' => ['required', 'date'],
        'opacity' => ['required', 'integer', 'min:10', 'max:100'],
    ]);

    $generateDate = $request->generate_date;
    $opacity = (int) $request->opacity;

    $images = UploadedImage::whereDate('upload_date', $generateDate)->get();

    if ($images->isEmpty()) {
        return back()->with('success', 'Is date par koi image nahi mili.');
    }

    $logoPath = $request->file('logo')->store('uploads/logos/' . $generateDate, 'public');
    $logoFullPath = storage_path('app/public/' . $logoPath);

    if (!file_exists($logoFullPath)) {
        return back()->with('success', 'Logo file not found.');
    }

    $logoSource = @imagecreatefromstring(file_get_contents($logoFullPath));

    if (!$logoSource) {
        return back()->with('success', 'Logo image invalid hai.');
    }

    foreach ($images as $record) {
        $mainPath = storage_path('app/public/' . $record->original_image);

        if (!file_exists($mainPath)) {
            continue;
        }

        $image = @imagecreatefromstring(file_get_contents($mainPath));

        if (!$image) {
            continue;
        }

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $logoWidth = imagesx($logoSource);
        $logoHeight = imagesy($logoSource);

        $newLogoWidth = intval($imageWidth * 0.18);
        $newLogoHeight = intval(($logoHeight / $logoWidth) * $newLogoWidth);

        $resizedLogo = imagecreatetruecolor($newLogoWidth, $newLogoHeight);

        imagealphablending($resizedLogo, false);
        imagesavealpha($resizedLogo, true);

        $transparent = imagecolorallocatealpha($resizedLogo, 0, 0, 0, 127);
        imagefill($resizedLogo, 0, 0, $transparent);

        imagecopyresampled(
            $resizedLogo,
            $logoSource,
            0,
            0,
            0,
            0,
            $newLogoWidth,
            $newLogoHeight,
            $logoWidth,
            $logoHeight
        );

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $margin = 30;

        $x = match ($request->position) {
            'top-left', 'bottom-left' => $margin,
            'top-right', 'bottom-right' => $imageWidth - $newLogoWidth - $margin,
        };

        $y = match ($request->position) {
            'top-left', 'top-right' => $margin,
            'bottom-left', 'bottom-right' => $imageHeight - $newLogoHeight - $margin,
        };

        imagecopymerge(
            $image,
            $resizedLogo,
            $x,
            $y,
            0,
            0,
            $newLogoWidth,
            $newLogoHeight,
            $opacity
        );

        $generatedFolder = 'uploads/generated/' . $generateDate;
        Storage::disk('public')->makeDirectory($generatedFolder);

        $generatedName = $generatedFolder . '/generated_' . uniqid() . '_' . $record->id . '.jpg';
        $savePath = storage_path('app/public/' . $generatedName);

        imagejpeg($image, $savePath, 85);

        imagedestroy($image);
        imagedestroy($resizedLogo);

        $record->update([
            'logo_image' => $logoPath,
            'position' => $request->position,
            'generated_image' => $generatedName,
        ]);

        gc_collect_cycles();
    }

    imagedestroy($logoSource);

    return redirect()
        ->route('image.tool', ['date' => $generateDate])
        ->with('success', 'Selected date ke saare images generate ho gaye.');
}


}