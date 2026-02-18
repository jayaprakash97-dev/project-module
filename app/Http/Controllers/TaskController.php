<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role == 'admin') {
            $tasks = Task::with(['project', 'employee'])->get();
        } elseif ($user->role == 'manager') {
            $tasks = Task::with(['project', 'employee'])
                ->whereHas('project', function ($q) use ($user) {
                    $q->where('manager_id', $user->id);
                })->get();
        } else {
            $tasks = Task::with(['project', 'employee'])
                ->where('employee_id', $user->id)
                ->get();
        }

        $projects = Project::all();
        $employees = User::where('role', 'employee')->get();

        return view('tasks.index', compact('tasks', 'projects', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_name' => 'required',
            'project_id' => 'required',
            'employee_id' => 'required',
            'due_date' => 'nullable|date'
        ]);

        Task::create($request->all());

        return redirect()->route('tasks.index')
            ->with('success', 'Task Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $user = auth()->user();

        if ($user->role == 'employee') {

            if ($task->employee_id != $user->id) {
                abort(403);
            }

            $task->update([
                'status' => $request->status
            ]);
        } else {
            $task->update($request->all());
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task Updated Successfully');
    }

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task Deleted Successfully');
    }
}
