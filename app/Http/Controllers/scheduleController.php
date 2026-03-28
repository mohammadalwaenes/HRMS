<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     */
    public function index()
    {
        $schedules = Schedule::with('employees.position')->get();
        return response()->json($schedules);
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'scheduleName' => 'required|string|max:255|unique:schedules,name',
            'days' => 'required|array|min:1',
            'days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
        ]);

        $schedule = Schedule::create([
            'name' => $data['scheduleName'],
            'start_time' => $data['startTime'],
            'end_time' => $data['endTime'],
            'days_of_week' => implode(',', $data['days']),
        ]);

        return response()->json([
            'message' => 'Schedule created successfully',
            'schedule' => $schedule
        ], 201);
    }

    /**
     * Display a specific schedule.
     */
    public function show($id)
    {
        $schedule = Schedule::with('employees.position')->findOrFail($id);
        return response()->json($schedule);
    }

    /**
     * Update the specified schedule.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'scheduleName' => 'required|string|max:255',
            'days' => 'required|array|min:1',
            'days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
        ]);

        $schedule = Schedule::findOrFail($id);
        $schedule->update([
            'name' => $data['scheduleName'],
            'start_time' => $data['startTime'],
            'end_time' => $data['endTime'],
            'days_of_week' => implode(',', $data['days']),
        ]);

        return response()->json([
            'message' => 'Schedule updated successfully',
            'schedule' => $schedule
        ]);
    }

    /**
     * Delete a schedule.
     */
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return response()->json([
            'message' => 'Schedule deleted successfully'
        ]);
    }

    /**
     * List employees assigned to a schedule.
     */
    public function assignedEmployees($id)
    {
        $schedule = Schedule::with('employees.position')->findOrFail($id);
        $employees = $schedule->employees()->paginate(10);

        return response()->json([
            'schedule' => $schedule->name,
            'employees' => $employees
        ]);
    }

    /**
     * Show schedule statistics.
     */
    public function statistics()
    {
        $schedules = Schedule::with('employees.position')->get();

        $stats = $schedules->map(function ($schedule) {
            $averageSalary = $schedule->employees->avg('salary');
            $positionsCount = $schedule->employees
                ->groupBy('position.name')
                ->map(fn($group) => $group->count());

            return [
                'name' => $schedule->name,
                'employeeCount' => $schedule->employees->count(),
                'averageSalary' => $averageSalary,
                'positionsCount' => $positionsCount
            ];
        });

        $totalEmployees = $schedules->sum(fn($s) => $s->employees->count());
        $highestPayingSchedule = $stats->sortByDesc('averageSalary')->first();
        $lowestPayingSchedule = $stats->sortBy('averageSalary')->first();

        return response()->json([
            'totalEmployees' => $totalEmployees,
            'highestPayingSchedule' => $highestPayingSchedule,
            'lowestPayingSchedule' => $lowestPayingSchedule,
            'scheduleStats' => $stats
        ]);
    }
}