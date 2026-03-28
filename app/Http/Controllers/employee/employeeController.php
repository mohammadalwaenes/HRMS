<?php

namespace App\Http\Controllers\employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Schedule;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\CreateEmployee;
use App\Http\Requests\UpdateEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    // GET /api/employees
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Employee::query();

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->has('position') && $request->position != '') {
            $query->where('position_id', $request->position);
        }

        if ($request->has('salary_sort') && in_array($request->salary_sort, ['asc', 'desc'])) {
            $query->orderBy('salary', $request->salary_sort);
        }

        $query->where("training", 0);

        $data = $query->paginate(15);

        return response()->json([
            'data' => EmployeeResource::collection($data),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ],
        ]);
    }

    // GET /api/employees/{id}
    public function show($id)
    {
        $employee = Employee::with('vacations', 'position', 'schedule')->findOrFail($id);

        return response()->json(new EmployeeResource($employee));
    }

    // POST /api/employees
    public function store(CreateEmployee $request)
    {
        $data = $request->validated();
        $data['training'] = isset($data['training']) && $data['training'] == "on" ? 1 : 0;

        if ($request->hasFile('cv')) {
            $data['cv'] = $request->file('cv')->storePublicly('cv', 'public');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storePublicly('images', 'public');
        }

        $employee = Employee::create([
            ...$data,
            'position_id' => $request->input('position'),
            'start_date' => now(),
        ]);

        return response()->json([
            'message' => 'Employee created successfully',
            'employee' => new EmployeeResource($employee)
        ], 201);
    }

    // PUT /api/employees/{id}
    public function update(UpdateEmployee $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('cv')) {
            if ($employee->cv) Storage::disk('public')->delete($employee->cv);
            $data["cv"] = $request->file('cv')->storePublicly('cv', 'public');
        }

        if ($request->hasFile('image')) {
            if ($employee->image) Storage::disk('public')->delete($employee->image);
            $data["image"] = $request->file('image')->storePublicly('images', 'public');
        }

        $employee->update($data);

        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => new EmployeeResource($employee)
        ]);
    }

    // DELETE /api/employees/{id}
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully'
        ]);
    }

    // POST /api/employees/{id}/restore
    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();

        return response()->json(['message' => 'Employee restored successfully!']);
    }

    // POST /api/employees/{id}/assign-schedule
    public function assignSchedule(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->schedule_id = $request->input('schedule_id');
        $employee->save();

        return response()->json([
            'message' => 'Schedule assigned successfully',
            'employee' => new EmployeeResource($employee)
        ]);
    }

    // POST /api/employees/{id}/unassign-schedule
    public function unassignSchedule($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->schedule_id = null;
        $employee->save();

        return response()->json([
            'message' => 'Schedule unassigned successfully',
            'employee' => new EmployeeResource($employee)
        ]);
    }

    // POST /api/employees/{id}/upload-cv
    public function uploadCv(Request $request, $id)
    {
        $request->validate(['cvFile' => 'required|file|mimes:pdf|max:2048']);
        $employee = Employee::findOrFail($id);

        if ($request->hasFile('cvFile')) {
            if ($employee->cv && Storage::disk('public')->exists($employee->cv)) {
                Storage::disk('public')->delete($employee->cv);
            }

            $employee->cv = $request->file('cvFile')->store('cv', 'public');
            $employee->save();

            return response()->json(['message' => 'CV uploaded successfully', 'employee' => new EmployeeResource($employee)]);
        }

        return response()->json(['message' => 'CV upload failed'], 400);
    }

    // POST /api/employees/{id}/upload-image
    public function uploadImage(Request $request, $id)
    {
        $request->validate(['image' => 'required|image|max:2048']);
        $employee = Employee::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($employee->image && Storage::disk('public')->exists($employee->image)) {
                Storage::disk('public')->delete($employee->image);
            }

            $employee->image = $request->file('image')->store('images', 'public');
            $employee->save();

            return response()->json(['message' => 'Image uploaded successfully', 'employee' => new EmployeeResource($employee)]);
        }

        return response()->json(['message' => 'Image upload failed'], 400);
    }

    // DELETE /api/employees/{id}/delete-cv
    public function deleteCv($id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee->cv && Storage::disk('public')->exists($employee->cv)) {
            Storage::disk('public')->delete($employee->cv);
            $employee->cv = null;
            $employee->save();

            return response()->json(['message' => 'CV deleted successfully', 'employee' => new EmployeeResource($employee)]);
        }

        return response()->json(['message' => 'No CV found to delete'], 404);
    }

    // DELETE /api/employees/{id}/delete-image
    public function deleteImage($id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee->image && Storage::disk('public')->exists($employee->image)) {
            Storage::disk('public')->delete($employee->image);
            $employee->image = null;
            $employee->save();

            return response()->json(['message' => 'Image deleted successfully', 'employee' => new EmployeeResource($employee)]);
        }

        return response()->json(['message' => 'No image found to delete'], 404);
    }

    // GET /api/employees/search?query=...
    public function search(Request $request)
    {
        $query = $request->input('query');
        if (!$query) return response()->json([]);

        $employees = Employee::where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%$query%")
              ->orWhere('last_name', 'like', "%$query%")
              ->orWhere('email', 'like', "%$query%");
        })->where('training', '<>', 2)->get();

        return response()->json(EmployeeResource::collection($employees));
    }

    // GET /api/employees/stats
    public function stats()
    {
        $totalEmployees = Employee::count();
        $trainingInProgress = Employee::where('training', 1)->count();
        $trainingCompleted = Employee::where('training', 2)->count();
        $trainingNotStarted = Employee::where('training', 0)->count();
        $genderDistribution = Employee::select('gender')->groupBy('gender')->selectRaw('COUNT(*) as count')->pluck('count', 'gender');
        $nationalityDistribution = Employee::select('nationality')->groupBy('nationality')->selectRaw('COUNT(*) as count')->pluck('count', 'nationality');
        $averageSalary = Employee::average('salary');
        $maxSalary = Employee::max('salary');
        $minSalary = Employee::min('salary');

        return response()->json([
            'totalEmployees',
            'trainingInProgress',
            'trainingCompleted',
            'trainingNotStarted',
            'genderDistribution' => $genderDistribution,
            'nationalityDistribution' => $nationalityDistribution,
            'averageSalary' => $averageSalary,
            'maxSalary' => $maxSalary,
            'minSalary' => $minSalary,
        ]);
    }
}