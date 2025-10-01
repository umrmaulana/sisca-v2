<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\P3k;
use App\Models\P3kLocation;
use Illuminate\Http\Request;

class P3kMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'item'); // default ke 'item'

        $stocks = null;
        $locations = P3kLocation::all();

        if ($type === 'item') {
            $stocks = P3k::all();
        } elseif ($type === 'location') {
            $locations = P3kLocation::all();
        }

        return view('p3k.master.index', [
            'type' => $type,
            'stocks' => $stocks,
            'locations' => $locations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'item') {
            P3k::create($request->only(['item', 'location_id', 'tag_number', 'expired_at', 'standard_stock', 'actual_stock']));
        } elseif ($type === 'location') {
            P3kLocation::create($request->only('location'));
        }

        return redirect()->route('p3k.master.store', ['type' => $type])->with('success', 'Data added successfully');

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
        $type = $request->input('type');

        if ($type === 'item') {
            $item = P3k::findOrFail($id);
            $item->update($request->only(['item', 'location_id', 'tag_number', 'expired_at', 'standard_stock', 'actual_stock']));
        } elseif ($type === 'location') {
            $location = P3kLocation::findOrFail($id);
            $location->update($request->only('location'));
        }

        return redirect()->route('p3k.master.index', ['type' => $type])->with('success', 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $type = $request->input('type');

        if ($type === 'item') {
            P3k::destroy($id);
        } elseif ($type === 'location') {
            P3kLocation::destroy($id);
        }

        return redirect()->route('p3k.master.index', ['type' => $type])->with('success', 'Data deleted successfully');
    }
}

