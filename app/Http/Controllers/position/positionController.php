<?php

namespace App\Http\Controllers\position;

use App\Http\Controllers\Controller;
use App\Http\Resources\PositionResource;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    
    public function index()
    {
        $positions = Position::all();
        return response()->json([
            'data' => PositionResource::collection($positions)
        ]);
    }

    
    public function show($id)
    {
        $position = Position::findOrFail($id);
        return response()->json(new PositionResource($position));
    }

   
    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "salary" => "required|numeric"
        ]);

        $position = Position::create($data);

        return response()->json([
            'message' => 'Position created successfully',
            'position' => new PositionResource($position)
        ], 201);
    }

    
    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        $data = $request->validate([
            "name" => "required|string|max:255",
            "salary" => "required|numeric"
        ]);

        $position->update($data);

        return response()->json([
            'message' => 'Position updated successfully',
            'position' => new PositionResource($position)
        ]);
    }

    
    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return response()->json([
            'message' => 'Position deleted successfully'
        ]);
    }

    
    public function showEmployeesByPosition(Request $request, $positionId)
    {
        $status = $request->query('status');

        if (!$status) {
            return response()->json(['message' => 'Status query parameter is required'], 400);
        }

        $trainingId = match($status) {
            'employee' => 0,
            'trainee' => 1,
            'trained' => 2,
            default => null
        };

        if ($trainingId === null) {
            return response()->json(['message' => 'Invalid status value'], 400);
        }

        $position = Position::findOrFail($positionId);

        $employees = $position->all_employees()
            ->where('training', $trainingId)
            ->paginate(10);

        return response()->json([
            'position' => new PositionResource($position),
            'employees' => $employees
        ]);
    }
}