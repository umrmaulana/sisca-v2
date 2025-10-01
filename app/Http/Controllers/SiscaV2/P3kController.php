<?php

namespace App\Http\Controllers\SiscaV2;

use App\Http\Controllers\Controller;
use App\Models\SiscaV2\P3k;
use App\Models\SiscaV2\P3kAccident;
use App\Models\SiscaV2\P3kHistory;
use App\Models\SiscaV2\P3kLocation;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class P3kController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $locations = P3kLocation::all();
        $query = $request->input('query');

        $location_id = $request->input('location_id', '1');
        $month = $request->input('month');
        $year = $request->input('year');

        $stocks = collect();
        $location = null;

        if ($location_id) {
            // Query untuk filter lokasi, bulan, tahun, dan item search
            $stocks = P3k::where('location_id', $location_id)
                ->when($query, function ($q) use ($query) {
                    $q->where('item', 'like', '%' . $query . '%');
                })
                ->when($month, function ($q) use ($month) {
                    $q->whereMonth('created_at', $month);
                })
                ->when($year, function ($q) use ($year) {
                    $q->whereYear('created_at', $year);
                })
                ->get();

            $location = P3kLocation::findOrFail($location_id);
        }

        // Ambil data history yang ada
        $histories = P3kHistory::with(['p3k', 'accident.masterAccident', 'accident.department'])
            ->latest()
            ->take(50)
            ->get();

        return view('sisca-v2.p3k.monitoring-stock.index', compact('locations', 'stocks', 'location', 'histories', 'query', 'month', 'year'));
    }

    public function filterHistory(Request $request)
    {
        $location_id = $request->location_id;
        $month = $request->month;
        $year = $request->year;

        $histories = P3kHistory::query()
            ->when($location_id, function ($query) use ($location_id) {
                $query->whereHas('p3k', function ($q) use ($location_id) {
                    $q->where('location_id', $location_id);
                });
            })
            ->when($month, function ($query) use ($month) {
                $query->whereMonth('updated_at', $month);
            })
            ->when($year, function ($query) use ($year) {
                $query->whereYear('updated_at', $year);
            })
            ->latest()
            ->get();

        $historyHtml = view('sisca-v2.p3k._partials.history-table', compact('histories'))->render();

        return response()->json([
            'histories' => $historyHtml,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $location_id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stocks = P3k::findOrFail($id);
        return view('sisca-v2.p3k.monitoring-stock.edit', compact('stocks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Mulai transaksi
        DB::beginTransaction();

        try {
            // Temukan stok p3k yang akan diupdate
            $stocks = P3k::findOrFail($id);

            // Validasi input
            $validateData = $request->validate([
                'actual_stock' => 'required|integer|min:1',
                'expired_at' => 'nullable|date',
            ]);

            $old_stock = $stocks->actual_stock;

            // Update stok
            $stocks->update([
                'actual_stock' => $validateData['actual_stock'],
                'expired_at' => $validateData['expired_at'] ?? null,
            ]);

            // Tambahkan entry ke p3k_history
            $historyData = [
                'p3k_id' => $stocks->id,
                'npk' => auth()->user()->npk,  // Mengambil NPK pengguna yang sedang login
                'action' => 'restock',  // Atur action sesuai kebutuhan
                'quantity' => $validateData['actual_stock'] - $old_stock,
            ];

            P3kHistory::create($historyData);  // Insert ke p3k_history

            // Commit transaksi
            DB::commit();

            return redirect()->route('sisca-v2.p3k.monitoring-stock.index')->with('success', 'Data updated and history saved.');
        } catch (\Exception $e) {
            // Jika ada error, rollback transaksi
            DB::rollBack();
            return back()->withErrors('Failed to update data and history: ' . $e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function notifications()
    {
        // low stock
        $lowStock = P3k::whereColumn('actual_stock', '<', 'standard_stock')->get();

        // expired
        $expired = P3k::whereNotNull('expired_at')
            ->whereDate('expired_at', '<', now())
            ->get();

        $count = $lowStock->count() + $expired->count();

        $response = [
            'count' => $count,
            'expired' => $expired->map(function ($item) {
                $location = P3kLocation::find($item->location_id);
                return [
                    'name' => $item->item,
                    'expired_date' => $item->expired_at ? \Carbon\Carbon::parse($item->expired_at)->format('d-m-Y') : null,
                    'location' => $location ? $location->location : 'Location not found',

                ];
            }),
            'low_stock' => $lowStock->map(function ($item) {
                $location = P3kLocation::find($item->location_id);
                return [
                    'name' => $item->item,
                    'stock' => $item->actual_stock,
                    'minimum_stock' => $item->standard_stock,
                    'location' => $location ? $location->location : 'Location not found',
                ];
            }),
        ];

        return response()->json($response);

    }

    public static function dashboard(Request $request)
    {
        //STOK
        // ambil stok rendah
        $lowStock = P3k::whereColumn('actual_stock', '<', 'standard_stock')->get();

        // ambil stok expired
        $expiredStock = P3k::whereDate('expired_at', '<', Carbon::today())->get();

        // data untuk chart donut
        $lowStockCount = $lowStock->count();
        $expiredStockCount = $expiredStock->count();

        //ACCIDENT
        $year = $request->input('year', date('Y'));
        $month = $request->input('month'); // null = all months

        // Chart: hitung jumlah accident per bulan berdasarkan created_at pada tahun yg dipilih
        $accidentPerMonth = DB::table('accidents')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Normalisasi jadi array 1..12
        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartData[$m] = isset($accidentPerMonth[$m]) ? (int) $accidentPerMonth[$m] : 0;
        }

        //Table Accident
        $query = P3kAccident::with(relations: 'masterAccident');
        $accidents = $query->with('masterAccident', 'department', 'location')
            ->when($year, fn($q) => $q->whereYear('accidents.created_at', $year))
            ->when($month, fn($q) => $q->whereMonth('accidents.created_at', $month))->orderBy('created_at', 'desc')
            ->get();

        return view('sisca-v2.p3k.dashboard', compact(
            'lowStock',
            'expiredStock',
            'lowStockCount',
            'expiredStockCount',
            'chartData',
            'accidents',
            'year',
            'month'
        ));
    }
}
