<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\MasterAccident;
use App\Models\P3k;
use App\Models\P3kHistory;
use App\Models\P3kLocation;
use DB;
use Illuminate\Http\Request;

class P3kHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $locations = P3kLocation::all();
        $accidents = MasterAccident::all();
        $departments = Department::all();

        return view('p3k.transaction-and-history.index', compact('locations', 'departments', 'accidents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'accident_id' => 'required|exists:accidents,id',
        ]);

        DB::beginTransaction();

        try {
            // Tangkap accident
            $accidentId = $request->input('accident_id');

            foreach ($request->items as $id => $itemData) {
                if (!isset($itemData['selected']) || empty($itemData['quantity'])) {
                    continue;
                }

                $qty = (int) $itemData['quantity'];

                $p3k = P3k::findOrFail($id);

                if ($p3k->actual_stock < $qty) {
                    throw new \Exception("Stok: {$p3k->item} not enough.");
                }

                // Update stock
                $p3k->actual_stock -= $qty;
                $p3k->save();

                // Simpan history
                P3kHistory::create([
                    'p3k_id' => $p3k->id,
                    'npk' => auth()->user()->npk,
                    'action' => 'take',
                    'quantity' => $qty,
                    'accident_id' => $accidentId,
                ]);
            }

            DB::commit();
            return redirect()->route('p3k.transaction-and-history.index')->with('success', 'Transaction success');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $location_id)
    {
        $queryStr = $request->input('query');

        $query = P3kHistory::with(relations: 'p3k');

        if ($queryStr) {
            $query->whereHas('p3k', function ($q) use ($queryStr) {
                $q->where('item', 'like', '%' . $queryStr . '%');
            });
        }

        $histories = $query->with(
            'p3k',
            'accident.masterAccident',
            'accident.department')
            ->orderBy('updated_at', 'desc')
            ->where('action', 'take')
            ->latest()
            ->get();

        $items = P3k::where('location_id', $location_id)
            ->when($queryStr, function ($q) use ($queryStr) {
                $q->where('item', 'like', '%' . $queryStr . '%');
            })
            ->get();

        $locations = P3kLocation::findOrFail($location_id);

        return view('p3k.transaction-and-history.show', compact('histories', 'items', 'locations'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $history = P3kHistory::find($id);
        $history->delete();

        return back()->with('success', 'History deleted successfully');
    }
}
