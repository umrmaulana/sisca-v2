<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\P3kAccident;
use Illuminate\Http\Request;

class P3kAccidentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'npk' => 'required|string|max:50',
            'location_id' => 'required|exists:p3k_location,id',
            'accident_id' => 'required|exists:master_data_accident,id',
            'department_id' => 'required|exists:departments,id',
            'npk_korban' => 'required|string|max:255',
            'nama_korban' => 'required|string|max:100',
        ]);

        $accident = P3kAccident::create([
            'reported_by' => $validated['npk'],
            'location_id' => $validated['location_id'],
            'accident_id' => $validated['accident_id'],
            'department_id' => $validated['department_id'],
            'victim_npk' => $validated['npk_korban'],
            'victim_name' => $validated['nama_korban'],
        ]);

        return redirect()->route('p3k.transaction-history.show', [
            'location_id' => $validated['location_id'],
            'department_id' => $validated['department_id'],
            'accident_id' => $validated['accident_id'],
            'accident_id' => $accident->id
        ])->with('success', 'Successfully save accident data.');
    }
}
