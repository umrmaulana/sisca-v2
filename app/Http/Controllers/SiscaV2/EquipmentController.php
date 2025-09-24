<?php

namespace App\Http\Controllers\SiscaV2;

use Illuminate\Routing\Controller;
use App\Models\SiscaV2\Equipment;
use App\Models\SiscaV2\EquipmentType;
use App\Models\SiscaV2\Location;
use App\Models\SiscaV2\PeriodCheck;
use App\Models\SiscaV2\Company;
use App\Models\SiscaV2\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class EquipmentController extends Controller
{
    public function __construct()
    {
        // Only Admin can create, update, delete
        $this->middleware('role:Admin', ['except' => ['index', 'show']]);
        // All roles can view (index, show)
        $this->middleware('role:Admin,Supervisor,Management', ['only' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        $user = auth('sisca-v2')->user();

        $query = Equipment::with([
            'equipmentType',
            'location',
            'periodCheck',
            'inspections.user' => function ($query) {
                $query->latest()->limit(1); // Get only the latest inspection's user
            }
        ]);

        // Apply company filter for non-admin users
        if ($user->role !== 'Admin' && $user->role !== 'Management' && $user->company_id) {
            $query->whereHas('location', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('equipment_code', 'like', "%{$search}%")
                ->orWhereHas('equipmentType', function ($q) use ($search) {
                    $q->where('equipment_name', 'like', "%{$search}%");
                })
                ->orWhereHas('location', function ($q) use ($search) {
                    $q->where('equipment_code', 'like', "%{$search}%");
                });
        }

        // Filter by equipment type
        if ($request->filled('equipment_type_id')) {
            $query->where('equipment_type_id', $request->get('equipment_type_id'));
        }

        // Filter by company (only for Admin/Management)
        if ($request->filled('company_id') && ($user->role === 'Admin' || $user->role === 'Management')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('company_id', $request->get('company_id'));
            });
        }

        // Filter by area
        if ($request->filled('area_id')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('area_id', $request->get('area_id'));
            });
        }

        // Filter by period check
        if ($request->filled('period_check_id')) {
            $query->where('period_check_id', $request->get('period_check_id'));
        }

        // Filter by is_active
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }

        $equipments = $query->latest()->paginate(10);

        // Append query parameters to pagination links
        $equipments->appends($request->query());

        // For filter dropdowns
        $equipmentTypes = EquipmentType::where('is_active', true)->get();
        $periodChecks = PeriodCheck::where('is_active', true)->get();

        // Areas dropdown (available for all roles, filtered by company access)
        $areas = collect(); // Initialize as empty collection

        // For non-admin users, load areas from their assigned company
        if ($user->role !== 'Admin' && $user->role !== 'Management' && $user->company_id) {
            $areas = Area::with(['company'])
                ->where('company_id', $user->company_id)
                ->where('is_active', true)
                ->get();
        }
        // For admin/management, load areas based on selected company filter
        elseif (($user->role === 'Admin' || $user->role === 'Management') && $request->company_id) {
            $areas = Area::with(['company'])
                ->where('company_id', $request->company_id)
                ->where('is_active', true)
                ->get();
        }

        // Companies dropdown (only for Admin/Management)
        $companies = [];
        if ($user->role === 'Admin' || $user->role === 'Management') {
            $companies = Company::where('is_active', true)->get();
        }

        return view('sisca-v2.equipments.index', compact('equipments', 'equipmentTypes', 'areas', 'companies', 'periodChecks'));
    }

    public function create()
    {
        $equipmentTypes = EquipmentType::where('is_active', true)->get();
        $companies = Company::where('is_active', true)->get();
        $areas = Area::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->get();
        $periodChecks = PeriodCheck::where('is_active', true)->get();
        return view('sisca-v2.equipments.create', compact('equipmentTypes', 'companies', 'areas', 'locations', 'periodChecks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_code' => 'required|string|max:20|unique:tm_equipments',
            'description' => 'nullable|string|max:255',
            'equipment_type_id' => 'required|exists:tm_equipment_types,id',
            'location_id' => 'required|exists:tm_locations_new,id',
            'period_check_id' => 'nullable|exists:tm_period_checks,id',
            'expired_date' => 'nullable|date',
            'is_active' => 'required|in:0,1',
        ]);

        // Get location with company and area info for QR code
        $location = Location::with(['company', 'area'])->find($request->location_id);
        $companyName = $location->company->company_name ?? null;
        $areaName = $location->area->area_name ?? null;

        // Generate QR Code with text
        $qrCodePath = $this->generateQrCode($request->equipment_code, $companyName, $areaName);

        // Create equipment
        $equipment = Equipment::create([
            'equipment_code' => $request->equipment_code,
            'description' => $request->description,
            'equipment_type_id' => $request->equipment_type_id,
            'location_id' => $request->location_id,
            'period_check_id' => $request->period_check_id,
            'qrcode' => $qrCodePath,
            'expired_date' => $request->expired_date,
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->route('sisca-v2.equipments.index')
            ->with('success', 'Equipment created successfully with QR code generated.');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load(['equipmentType', 'location.company', 'location.area', 'periodCheck', 'inspections.user']);
        return view('sisca-v2.equipments.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        $equipmentTypes = EquipmentType::where('is_active', true)->get();
        $companies = Company::where('is_active', true)->get();
        $areas = Area::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->get();
        $periodChecks = PeriodCheck::where('is_active', true)->get();
        return view('sisca-v2.equipments.edit', compact('equipment', 'equipmentTypes', 'companies', 'areas', 'locations', 'periodChecks'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $request->validate([
            'equipment_code' => 'required|string|max:20|unique:tm_equipments,equipment_code,' . $equipment->id,
            'description' => 'nullable|string|max:255',
            'equipment_type_id' => 'required|exists:tm_equipment_types,id',
            'location_id' => 'required|exists:tm_locations_new,id',
            'period_check_id' => 'nullable|exists:tm_period_checks,id',
            'expired_date' => 'nullable|date',
            'is_active' => 'required|in:0,1',
        ]);

        $updateData = [
            'equipment_code' => $request->equipment_code,
            'description' => $request->description,
            'equipment_type_id' => $request->equipment_type_id,
            'location_id' => $request->location_id,
            'period_check_id' => $request->period_check_id,
            'expired_date' => $request->expired_date,
            'is_active' => (bool) $request->is_active,
        ];

        // If equipment code changed, generate new QR code
        if ($equipment->equipment_code !== $request->equipment_code) {
            // Delete old QR code
            if ($equipment->qrcode && Storage::disk('public')->exists($equipment->qrcode)) {
                Storage::disk('public')->delete($equipment->qrcode);
            }

            // Get location with company and area info for QR code
            $location = Location::with(['company', 'area'])->find($request->location_id);
            $companyName = $location->company->company_name ?? null;
            $areaName = $location->area->area_name ?? null;

            // Generate new QR code with text
            $updateData['qrcode'] = $this->generateQrCode($request->equipment_code, $companyName, $areaName);
        }

        $equipment->update($updateData);

        return redirect()->route('sisca-v2.equipments.index')
            ->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        // Delete QR code file if exists
        if ($equipment->qrcode && Storage::disk('public')->exists($equipment->qrcode)) {
            Storage::disk('public')->delete($equipment->qrcode);
        }

        $equipment->delete();

        return redirect()->route('sisca-v2.equipments.index')
            ->with('success', 'Equipment deleted successfully.');
    }

    /**
     * Generate QR code for equipment with text overlay using Endroid QrCode
     */
    private function generateQrCode($equipmentCode, $companyName = null, $areaName = null)
    {
        // Create directory if not exists
        $qrDir = 'sisca-v2/qrcode';
        if (!Storage::disk('public')->exists($qrDir)) {
            Storage::disk('public')->makeDirectory($qrDir);
        }

        // Generate QR code filename
        $qrFileName = 'QR_' . $equipmentCode . '_' . time() . '.png';
        $qrPath = $qrDir . '/' . $qrFileName;

        try {
            // Create QR code using Endroid QrCode (uses GD library)
            $qrCode = QrCode::create($equipmentCode)
                ->setSize(250)
                ->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Get QR code as image resource
            $qrImage = imagecreatefromstring($result->getString());

            // Create image canvas with text space
            $canvasWidth = 300;
            $canvasHeight = 350;
            $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);

            // Set background color to white
            $white = imagecolorallocate($canvas, 255, 255, 255);
            $black = imagecolorallocate($canvas, 0, 0, 0);
            $gray = imagecolorallocate($canvas, 100, 100, 100);
            imagefill($canvas, 0, 0, $white);

            // Calculate position to center QR code
            $qrWidth = imagesx($qrImage);
            $qrHeight = imagesy($qrImage);
            $qrX = ($canvasWidth - $qrWidth) / 2;
            $qrY = 10;

            // Copy QR code to canvas
            imagecopy($canvas, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight);

            // Add text below QR code (without "QR CODE" title)
            $textY = $qrY + $qrHeight + 20;
            $fontSize = 4; // Built-in font size 1-5

            // Add equipment code directly
            $equipText = $equipmentCode;
            $equipWidth = imagefontwidth($fontSize) * strlen($equipText);
            $equipX = ($canvasWidth - $equipWidth) / 2;
            imagestring($canvas, $fontSize, $equipX, $textY, $equipText, $black);

            // Add company and area info if available
            if ($companyName && $areaName) {
                $textY += 25;
                $locationText = $companyName . " - " . $areaName;
                $fontSize2 = 3; // Smaller font for location
                $locationWidth = imagefontwidth($fontSize2) * strlen($locationText);
                $locationX = ($canvasWidth - $locationWidth) / 2;
                imagestring($canvas, $fontSize2, $locationX, $textY, $locationText, $gray);
            } elseif ($companyName) {
                $textY += 25;
                $fontSize2 = 3;
                $companyWidth = imagefontwidth($fontSize2) * strlen($companyName);
                $companyX = ($canvasWidth - $companyWidth) / 2;
                imagestring($canvas, $fontSize2, $companyX, $textY, $companyName, $gray);
            }

            // Save image to storage
            ob_start();
            imagepng($canvas);
            $imageData = ob_get_contents();
            ob_end_clean();

            Storage::disk('public')->put($qrPath, $imageData);

            // Clean up memory
            imagedestroy($canvas);
            imagedestroy($qrImage);

        } catch (\Exception $e) {
            // Fallback: create simple text-based image
            $this->generateFallbackQrCode($equipmentCode, $companyName, $areaName, $qrPath);
        }

        return $qrPath;
    }

    /**
     * Fallback QR code generation if main method fails
     */
    private function generateFallbackQrCode($equipmentCode, $companyName, $areaName, $qrPath)
    {
        // Create simple image with text only
        $canvasWidth = 300;
        $canvasHeight = 350;
        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);

        // Set colors
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $black = imagecolorallocate($canvas, 0, 0, 0);
        $gray = imagecolorallocate($canvas, 100, 100, 100);
        imagefill($canvas, 0, 0, $white);

        // Draw border
        imagerectangle($canvas, 10, 10, $canvasWidth - 10, $canvasHeight - 10, $black);

        // Add text
        $textY = 50;
        $fontSize = 5;

        // Add "QR PLACEHOLDER" title
        $titleText = "QR PLACEHOLDER";
        $titleWidth = imagefontwidth($fontSize) * strlen($titleText);
        $titleX = ($canvasWidth - $titleWidth) / 2;
        imagestring($canvas, $fontSize, $titleX, $textY, $titleText, $black);

        // Add equipment code
        $textY += 100;
        $equipText = "Equipment Code:";
        $equipWidth = imagefontwidth(4) * strlen($equipText);
        $equipX = ($canvasWidth - $equipWidth) / 2;
        imagestring($canvas, 4, $equipX, $textY, $equipText, $gray);

        $textY += 30;
        $codeWidth = imagefontwidth($fontSize) * strlen($equipmentCode);
        $codeX = ($canvasWidth - $codeWidth) / 2;
        imagestring($canvas, $fontSize, $codeX, $textY, $equipmentCode, $black);

        // Add company and area info if available
        if ($companyName && $areaName) {
            $textY += 40;
            $locationText = $companyName . " - " . $areaName;
            $locationWidth = imagefontwidth(3) * strlen($locationText);
            $locationX = ($canvasWidth - $locationWidth) / 2;
            imagestring($canvas, 3, $locationX, $textY, $locationText, $gray);
        }

        // Save image to storage
        ob_start();
        imagepng($canvas);
        $imageData = ob_get_contents();
        ob_end_clean();

        Storage::disk('public')->put($qrPath, $imageData);

        // Clean up memory
        imagedestroy($canvas);
    }

    /**
     * Get areas by company ID for cascade dropdown
     */
    public function getAreasByCompany(Request $request)
    {
        $companyId = $request->get('company_id');
        $user = auth('sisca-v2')->user();

        if (!$companyId) {
            return response()->json(['areas' => []]);
        }

        // Check if user has access to this company
        if ($user->role !== 'Admin' && $user->role !== 'Management') {
            if ($user->company_id && $user->company_id != $companyId) {
                return response()->json(['error' => 'Unauthorized access to this company'], 403);
            }
        }

        try {
            $areas = Area::where('company_id', $companyId)
                ->where('is_active', true)
                ->get()
                ->map(function ($area) {
                    return [
                        'id' => $area->id,
                        'area_name' => $area->area_name
                    ];
                });

            return response()->json(['areas' => $areas]);
        } catch (\Exception $e) {
            \Log::error('Error loading areas by company: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading areas'], 500);
        }
    }

    /**
     * Get locations by area ID for cascade dropdown
     */
    public function getLocationsByArea(Request $request)
    {
        $areaId = $request->get('area_id');
        $locations = Location::where('area_id', $areaId)->where('is_active', true)->get();
        return response()->json($locations);
    }

    /**
     * Test QR code generation
     */
    public function testQrGeneration()
    {
        try {
            $equipmentCode = 'TEST-QR-' . time();
            $companyName = 'Test Company';
            $areaName = 'Test Area';

            $qrPath = $this->generateQrCode($equipmentCode, $companyName, $areaName);

            if (Storage::disk('public')->exists($qrPath)) {
                $qrUrl = asset('storage/' . $qrPath);
                return response()->json([
                    'success' => true,
                    'message' => 'QR Code generated successfully!',
                    'qr_path' => $qrPath,
                    'qr_url' => $qrUrl,
                    'equipment_code' => $equipmentCode
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code file not found after generation'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'QR Generation failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
