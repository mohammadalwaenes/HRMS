<?php

namespace App\Http\Controllers\trainee;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployee;
use App\Http\Requests\UpdateEmployee;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TraineeController extends Controller
{
    // GET /api/trainees
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Employee::query()->where('training', 1); // Trainees

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhereHas('position', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $trainees = $query->paginate(10);

        return response()->json($trainees);
    }

    // GET /api/trainees/trained
    public function trainedIndex(Request $request)
    {
        $search = $request->query('search');

        $query = Employee::query()->where('training', 2); // Trained employees

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhereHas('position', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $trained = $query->paginate(10);

        return response()->json($trained);
    }

    // GET /api/trainees/{id}
    public function show($id)
    {
        $trainee = Employee::where('id', $id)->where('training', 1)->firstOrFail();
        return response()->json(new EmployeeResource($trainee));
    }

    // PUT /api/trainees/{id}  => update trainee info
    public function update(UpdateEmployee $request, $id)
    {
        $data = $request->validated();
        $trainee = Employee::findOrFail($id);

        $trainee->position_id = $data['position'] ?? $trainee->position_id;

        if ($request->hasFile('cv')) {
            if ($trainee->cv) {
                Storage::disk('public')->delete($trainee->cv);
            }
            $data['cv'] = $request->file('cv')->store('cv', 'public');
        }

        if ($request->hasFile('image')) {
            if ($trainee->image) {
                Storage::disk('public')->delete($trainee->image);
            }
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $trainee->update($data);

        return response()->json([
            'message' => 'Trainee updated successfully',
            'trainee' => new EmployeeResource($trainee)
        ]);
    }

    // DELETE /api/trainees/{id}
    public function destroy($id)
    {
        $trainee = Employee::findOrFail($id);
        $trainee->delete();

        return response()->json(['message' => 'Trainee deleted successfully']);
    }

    // POST /api/trainees/{id}/end-training
    public function endTraining($id)
    {
        $trainee = Employee::where('id', $id)->where('training', 1)->firstOrFail();
        $trainee->training = 2; // Mark as trained
        $trainee->save();

        return response()->json([
            'message' => 'Trainee training ended successfully',
            'trainee' => new EmployeeResource($trainee)
        ]);
    }

    // POST /api/trainees/{id}/hire
    public function hire($id)
    {
        $trainee = Employee::whereIn('training', [1, 2])->findOrFail($id);
        $trainee->training = 0; // Mark as regular employee
        $trainee->start_date = now();
        $trainee->save();

        return response()->json([
            'message' => 'Trainee hired successfully',
            'employee' => new EmployeeResource($trainee)
        ]);
    }
}