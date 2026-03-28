<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VacationController extends Controller
{
    /**
     * Display a listing of vacations
     */
    public function index(Request $request)
    {
        $query = Vacation::with('employee');

        if ($request->has('upcoming') && $request->upcoming == 'true') {
            $query->where('start_date', '>', Carbon::today());
        } elseif ($request->has('ongoing') && $request->ongoing == 'true') {
            $query->where('start_date', '<=', Carbon::today())
                  ->where('end_date', '>=', Carbon::today());
        } elseif ($request->has('past') && $request->past == 'true') {
            $query->where('end_date', '<', Carbon::today());
        }

        $vacations = $query->paginate(10);

        return response()->json($vacations);
    }

    /**
     * Store a newly created vacation
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $vacation = Vacation::create($validatedData);

        return response()->json([
            'message' => 'Vacation successfully added!',
            'vacation' => $vacation
        ], 201);
    }

    /**
     * Show a single vacation
     */
    public function show($id)
    {
        $vacation = Vacation::with('employee')->findOrFail($id);

        return response()->json($vacation);
    }

    /**
     * Update the specified vacation
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'end_date' => 'required|date|after_or_equal:today',
        ]);

        $vacation = Vacation::findOrFail($id);
        $vacation->end_date = $request->end_date;
        $vacation->save();

        return response()->json([
            'message' => 'Vacation updated successfully!',
            'vacation' => $vacation
        ]);
    }

    /**
     * Remove the specified vacation
     */
    public function destroy($id)
    {
        $vacation = Vacation::findOrFail($id);
        $vacation->delete();

        return response()->json([
            'message' => 'Vacation deleted successfully!'
        ]);
    }

    /**
     * Get vacation statistics (API)
     */
    public function stats()
    {
        $vacationsPerMonth = DB::table('vacations')
            ->selectRaw('MONTH(start_date) as month, COUNT(*) as count')
            ->whereYear('start_date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $vacationStatus = DB::table('vacations')
            ->selectRaw("CASE
                WHEN start_date > CURRENT_DATE THEN 'Upcoming'
                WHEN end_date < CURRENT_DATE THEN 'Past'
                ELSE 'Ongoing'
            END as status, COUNT(*) as count")
            ->groupBy('status')
            ->get();

        return response()->json([
            'vacationsPerMonth' => $vacationsPerMonth,
            'vacationStatus' => $vacationStatus
        ]);
    }
}