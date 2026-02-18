<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role == 'admin') {
            $projects = Project::with(['manager', 'employees'])->get();
        } elseif ($user->role == 'manager') {
            $projects = Project::with(['manager', 'employees'])
                ->where('manager_id', $user->id)
                ->get();
        } else {
            $projects = Project::with(['manager', 'employees'])
                ->whereHas('employees', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->get();
        }

        $managers = User::where('role', 'manager')->get();
        $employees = User::where('role', 'employee')->get();

        return view('projects.index', compact('projects', 'managers', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_name' => 'required',
            'description'  => 'nullable',
            'due_date'     => 'nullable|date',
            'manager_id'   => 'required'
        ]);

        $project = Project::create([
            'project_name' => $request->project_name,
            'description'  => $request->description,
            'due_date'     => $request->due_date,
            'manager_id'   => $request->manager_id,
        ]);

        if ($request->employees) {
            $project->employees()->sync($request->employees);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'project_name' => 'required',
            'description'  => 'nullable',
            'due_date'     => 'nullable|date',
            'manager_id'   => 'required'
        ]);

        $project->update([
            'project_name' => $request->project_name,
            'description'  => $request->description,
            'due_date'     => $request->due_date,
            'manager_id'   => $request->manager_id,
        ]);

        $project->employees()->sync($request->employees ?? []);

        return redirect()->route('projects.index')
            ->with('success', 'Project Updated Successfully');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        $project->employees()->detach();

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project Deleted Successfully');
    }
}
