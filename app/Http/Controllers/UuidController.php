<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class UuidController extends Controller
{
    /**
     * Check if a UUID exists and return the employee info
     */
    public function checkUuid(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string'
        ]);

        $uuid = $request->input('uuid');

        // Validate UUID format
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid)) {
            return response()->json([
                'exists' => false,
                'message' => 'Invalid UUID format.'
            ], 422);
        }

        $employee = Employee::where('uuid', $uuid)->first();

        if ($employee) {
            return response()->json([
                'exists' => true,
                'message' => 'Employee found.',
                'employee' => $employee
            ]);
        }

        return response()->json([
            'exists' => false,
            'message' => 'UUID does not exist.'
        ], 404);
    }
}