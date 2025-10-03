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
            'location_id' => 'required|exists:tm_p3k_location,id',
            'accident_id' => 'nullable',
            'accident_other' => 'nullable|string|max:255',
            'department_id' => 'required|exists:tm_departments,id',
            'npk_korban' => 'required|string|max:255',
            'nama_korban' => 'required|string|max:100',
        ]);

        $accidentId = null;
        $accidentOther = null;

        // Cek apakah pilih "other"
        if ($request->input('accident_id') === 'other') {
            if (empty($request->accident_other)) {
                return back()->with('error', 'Please specify accident if you choose Other.');
            }
            $accidentOther = $request->accident_other;
        } elseif (!empty($request->accident_id)) {
            $request->validate([
                'accident_id' => 'nullable|exists:tm_accident,id'
            ]);
            $accidentId = $request->accident_id;
        }

        $accident = P3kAccident::create([
            'reported_by' => $validated['npk'],
            'location_id' => $validated['location_id'],
            'accident_id' => $accidentId,
            'accident_other' => $accidentOther,
            'department_id' => $validated['department_id'],
            'victim_npk' => $validated['npk_korban'],
            'victim_name' => $validated['nama_korban'],
        ]);

        return redirect()->route('p3k.transaction-history.show', [
            'location_id' => $validated['location_id'],
            'department_id' => $validated['department_id'],
            'accident_id' => $accident->id
        ])->with('success', 'Successfully save accident data.');
    }

}
