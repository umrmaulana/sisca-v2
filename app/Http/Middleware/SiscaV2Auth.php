<?php

use App\Http\Controllers\SiscaV2\PeriodCheckController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiscaV2\PlantController;
use App\Http\Controllers\SiscaV2\AreaController;
use App\Http\Controllers\SiscaV2\EquipmentTypeController;
use App\Http\Controllers\SiscaV2\LocationController;
use App\Http\Controllers\SiscaV2\EquipmentController;
use App\Http\Controllers\SiscaV2\UserController;
use App\Http\Controllers\SiscaV2\AuthController;
use App\Http\Controllers\SiscaV2\ChecksheetTemplateController;
use App\Http\Controllers\SiscaV2\ChecksheetController;
use App\Http\Controllers\SiscaV2\InspectionController;
use App\Http\Controllers\SiscaV2\DashboardController;

/*
|--------------------------------------------------------------------------
| SISCA V2 Routes
|--------------------------------------------------------------------------
|
| Here is where you can register SISCA V2 routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// Authentication Routes
Route::prefix('sisca-v2')->name('sisca-v2.')->group(function () {

    // ===========================================
    // 1. PUBLIC ROUTES (No Authentication Required)
    // ===========================================

    // Root redirect - redirect to dashboard if authenticated, login if not
    Route::get('/', function () {
        if (auth('sisca-v2')->check()) {
            return redirect()->route('sisca-v2.dashboard');
        }
        return redirect()->route('sisca-v2.login');
    })->name('home');

    // Login Routes (No middleware for login page)
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest:sisca-v2');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit')->middleware('guest:sisca-v2');

    // No Access Route
    Route::get('no-access', function () {
        return view('sisca-v2.errors.no-access');
    })->name('no-access');

    // ===========================================
    // 2. PROTECTED ROUTES (Authentication Required)
    // ===========================================

    Route::middleware('auth:sisca-v2')->group(function () {

        // Logout Route
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // ===============================
        // Dashboard Routes
        // ===============================
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard/areas-by-plant', [DashboardController::class, 'getAreasByPlant'])->name('dashboard.areas-by-plant');

        // ===============================
        // Checksheet Management
        // ===============================
        Route::prefix('checksheets')->name('checksheets.')->group(function () {
            Route::get('/', [ChecksheetController::class, 'index'])->name('index');
            Route::get('/create', [ChecksheetController::class, 'create'])->name('create');
            Route::post('/store', [ChecksheetController::class, 'store'])->name('store');
            Route::get('/show/{id}', [ChecksheetController::class, 'show'])->name('show');
            Route::get('/history', [ChecksheetController::class, 'history'])->name('history');
            Route::get('/equipment/{code}', [ChecksheetController::class, 'getEquipment'])->name('equipment');
            Route::get('/areas-by-plant', [ChecksheetController::class, 'getAreasByPlant'])->name('areas-by-plant');
            Route::post('/approve/{id}', [ChecksheetController::class, 'approve'])->name('approve');
            Route::get('/ng-history/{equipmentId}', [ChecksheetController::class, 'ngHistory'])->name('ng-history');
        });

        // ===============================
        // Mapping Area Management
        // ===============================
        Route::prefix('mapping-area')->name('mapping-area.')->group(function () {
            Route::get('/', [App\Http\Controllers\SiscaV2\MappingAreaController::class, 'index'])->name('index');
            Route::get('/areas-by-plant', [App\Http\Controllers\SiscaV2\MappingAreaController::class, 'getAreasByPlant'])->name('areas-by-plant');
        });

        // ===============================
        // Report Management (Supervisor/Management/Admin only)
        // ===============================

        // Summary Report Management
        Route::prefix('summary-report')->name('summary-report.')->group(function () {
            Route::get('/', [App\Http\Controllers\SiscaV2\SummaryReportController::class, 'index'])->name('index');
            Route::post('/{id}/approve', [App\Http\Controllers\SiscaV2\SummaryReportController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [App\Http\Controllers\SiscaV2\SummaryReportController::class, 'reject'])->name('reject');
            Route::post('/bulk-approve', [App\Http\Controllers\SiscaV2\SummaryReportController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-reject', [App\Http\Controllers\SiscaV2\SummaryReportController::class, 'bulkReject'])->name('bulk-reject');
            Route::get('/areas-by-plant', [App\Http\Controllers\SiscaV2\SummaryReportController::class, 'getAreasByPlant'])->name('areas-by-plant');
            Route::get('/export-pdf', [App\Http\Controllers\SiscaV2\SummaryReportController::class, 'exportPdf'])->name('export-pdf');
        });

        // NG History Management
        Route::prefix('ng-history')->name('ng-history.')->group(function () {
            Route::get('/', [ChecksheetController::class, 'ngHistoryIndex'])->name('index');
        });

        // ===============================
        // Master Data Management (Admin/Management Only)
        // ===============================

        // Plants Management
        Route::resource('plants', PlantController::class);

        // Areas Management
        Route::resource('areas', AreaController::class);

        // Equipment Types Management  
        Route::resource('equipment-types', EquipmentTypeController::class);

        // Period Checks Management
        Route::resource('period-checks', PeriodCheckController::class);

        // Checksheet Template Management
        Route::resource('checksheet-templates', ChecksheetTemplateController::class);
        Route::get('checksheet-templates/type/{type}', [ChecksheetTemplateController::class, 'getByType'])->name('checksheet-templates.by-type');
        Route::get('checksheet-templates/equipment/{type}', [ChecksheetTemplateController::class, 'getEquipmentList'])->name('checksheet-templates.equipment');
        Route::get('checksheet-templates/get-next-order/{equipmentTypeId}', [ChecksheetTemplateController::class, 'getNextOrder'])->name('checksheet-templates.get-next-order');

        // Locations Management
        Route::resource('locations', LocationController::class);
        Route::get('locations/areas/{plant}', [LocationController::class, 'getAreasByPlant'])->name('locations.areas-by-plant');
        Route::get('locations/plant/{plant}', [LocationController::class, 'getPlantData'])->name('locations.plant-data');

        // Equipments Management
        Route::get('equipments/areas-by-plant', [EquipmentController::class, 'getAreasByPlant'])->name('equipments.areas-by-plant');
        Route::get('equipments/locations-by-area', [EquipmentController::class, 'getLocationsByArea'])->name('equipments.locations-by-area');
        Route::get('equipments/test-qr', [EquipmentController::class, 'testQrGeneration'])->name('equipments.test-qr');
        Route::resource('equipments', EquipmentController::class);

        // ===============================
        // Inspections Management
        // ===============================
        Route::resource('inspections', InspectionController::class);
        Route::get('inspections/ng-history', [InspectionController::class, 'getNgHistory'])->name('inspections.ng-history');

        // ===============================
        // User Management (Admin Only)
        // ===============================
        Route::resource('users', UserController::class);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // ===============================
        // Profile Management (All Roles)
        // ===============================
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\SiscaV2\ProfileController::class, 'show'])->name('show');
            Route::get('/edit', [App\Http\Controllers\SiscaV2\ProfileController::class, 'edit'])->name('edit');
            Route::put('/update', [App\Http\Controllers\SiscaV2\ProfileController::class, 'update'])->name('update');
            Route::get('/change-password', [App\Http\Controllers\SiscaV2\ProfileController::class, 'changePassword'])->name('change-password');
            Route::put('/update-password', [App\Http\Controllers\SiscaV2\ProfileController::class, 'updatePassword'])->name('update-password');
        });

        // ===============================
        // API Routes for AJAX calls
        // ===============================
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('plants', function () {
                return response()->json(\App\Models\SiscaV2\Plant::where('is_active', true)->get());
            })->name('plants');

            Route::get('areas', function () {
                return response()->json(\App\Models\SiscaV2\Area::where('is_active', true)->get());
            })->name('areas');

            Route::get('equipment-types', function () {
                return response()->json(\App\Models\SiscaV2\EquipmentType::where('is_active', true)->get());
            })->name('equipment-types');

            Route::get('equipments', function () {
                return response()->json(\App\Models\SiscaV2\Equipment::where('is_active', true)->with(['equipmentType', 'location'])->get());
            })->name('equipments');

            Route::get('locations', function () {
                return response()->json(\App\Models\SiscaV2\Location::where('is_active', true)->with(['plant', 'area'])->get());
            })->name('locations');

            Route::get('checksheet-templates', function () {
                return response()->json(\App\Models\SiscaV2\ChecksheetTemplate::where('is_active', true)->get());
            })->name('checksheet-templates');

            Route::get('inspections/{id}', function ($id) {
                $inspection = \App\Models\SiscaV2\Inspection::with([
                    'user',
                    'equipment.equipmentType',
                    'equipment.location',
                    'details.checksheetTemplate'
                ])->findOrFail($id);
                return response()->json($inspection);
            })->name('inspections.show');
        });
    });
});
