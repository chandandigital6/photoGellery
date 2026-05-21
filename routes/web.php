<?php
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\ImageToolController;

Route::view('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);

    Route::delete('roles/{role}/permissions/{permission}', [RoleController::class, 'removePermission'])
        ->name('roles.permissions.remove');

    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions/assign', [PermissionController::class, 'assign'])->name('permissions.assign');
    Route::post('permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
 Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');




    Route::get('/image-tool', [ImageToolController::class, 'index'])->name('image.tool');
Route::post('/image-tool/upload', [ImageToolController::class, 'upload'])->name('image.tool.upload');
Route::post('/image-tool/generate', [ImageToolController::class, 'generate'])->name('image.tool.generate');
});

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')
        ->name('invitations.accept');
});

require __DIR__.'/settings.php';