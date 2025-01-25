<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorData;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'gas_value' => 'required|numeric',
        ]);

        SensorData::create([
            'gas_value' => $request->gas_value,
        ]);

        return response()->json(['message' => 'Data stored successfully']);
    }
}
