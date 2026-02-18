<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    public function getEmployeeTasks(Request $request, $employee_id)
    {
        $query = Task::with(['project', 'employee'])
            ->where('employee_id', $employee_id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('due_date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        if ($request->filled('search')) {
            $query->where('task_name', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No tasks found for this employee',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee task list',
            'data' => $tasks
        ]);
    }
}
