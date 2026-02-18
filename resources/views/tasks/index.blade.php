<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="d-flex justify-content-between mb-3">
                <h4>Tasks List</h4>

                @if(in_array(auth()->user()->role,['admin','manager']))
                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    + Add Task
                </button>
                @endif
            </div>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @php
            $todayTasks = $tasks->filter(function($task) {
            return $task->due_date && \Carbon\Carbon::parse($task->due_date)->isToday();
            });
            @endphp

            @if($todayTasks->count() > 0)
            <div class="alert alert-warning">
                <strong>Tasks Due Today:</strong>
                <ul class="mb-0">
                    @foreach($todayTasks as $todayTask)
                    <li>
                        {{ $todayTask->task_name }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Task Name</th>
                        <th>Project</th>
                        <th>Assigned Employee</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($tasks as $key=>$task)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $task->task_name }}</td>
                        <td>{{ $task->project->project_name ?? '-' }}</td>
                        <td>{{ $task->employee->name ?? '-' }}</td>
                        <td>{{ $task->due_date }}</td>

                        <td>
                            <span class="badge
            @if($task->status == 'Completed') bg-success
            @elseif($task->status == 'In Progress') bg-warning
            @else bg-secondary
            @endif">
                                {{ $task->status }}
                            </span>
                        </td>

                        <td>

                            @if(in_array(auth()->user()->role,['admin','manager']))

                            <button class="btn btn-sm btn-dark"
                                data-bs-toggle="modal"
                                data-bs-target="#editTaskModal{{ $task->id }}">
                                Edit
                            </button>

                            <form action="{{ route('tasks.destroy',$task->id) }}"
                                method="POST"
                                style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </form>

                            @elseif(auth()->user()->role == 'employee' && $task->employee_id == auth()->id())

                            <form action="{{ route('tasks.update',$task->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <select name="status" class="form-select form-select-sm mb-1">
                                    <option value="Pending" {{ $task->status == 'Pending' ? 'selected':'' }}>
                                        Pending
                                    </option>
                                    <option value="In Progress" {{ $task->status == 'In Progress' ? 'selected':'' }}>
                                        In Progress
                                    </option>
                                    <option value="Completed" {{ $task->status == 'Completed' ? 'selected':'' }}>
                                        Completed
                                    </option>
                                </select>

                                <button class="btn btn-sm btn-dark">
                                    Update Status
                                </button>
                            </form>

                            @endif

                        </td>
                    </tr>


                    @if(in_array(auth()->user()->role,['admin','manager']))
                    <div class="modal fade" id="editTaskModal{{ $task->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5>Edit Task</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">

                                    <form action="{{ route('tasks.update',$task->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <input type="text" name="task_name" class="form-control mb-2"
                                            value="{{ $task->task_name }}" required>

                                        <select name="project_id" class="form-control mb-2">
                                            @foreach($projects as $project)
                                            <option value="{{ $project->id }}"
                                                {{ $task->project_id == $project->id ? 'selected':'' }}>
                                                {{ $project->project_name }}
                                            </option>
                                            @endforeach
                                        </select>

                                        <select name="employee_id" class="form-control mb-2">
                                            @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}"
                                                {{ $task->employee_id == $emp->id ? 'selected':'' }}>
                                                {{ $emp->name }}
                                            </option>
                                            @endforeach
                                        </select>

                                        <select name="status" class="form-control mb-2">
                                            <option value="pending" {{ $task->status == 'pending' ? 'selected':'' }}>Pending</option>
                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected':'' }}>In Progress</option>
                                            <option value="completed" {{ $task->status == 'completed' ? 'selected':'' }}>Completed</option>
                                        </select>

                                        <input type="date" name="due_date" class="form-control mb-2"
                                            value="{{ $task->due_date }}">

                                        <textarea name="description" class="form-control mb-2">
                                        {{ $task->description }}
                                        </textarea>

                                        <button type="submit" class="btn btn-dark">
                                            Update
                                        </button>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No Tasks Found</td>
                    </tr>
                    @endforelse

                </tbody>
            </table>


            @if(in_array(auth()->user()->role,['admin','manager']))
            <div class="modal fade" id="addTaskModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5>Add Task</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">

                            <form action="{{ route('tasks.store') }}" method="POST">
                                @csrf

                                <input type="text" name="task_name" class="form-control mb-2" placeholder="Task Name" required>

                                <select name="project_id" class="form-control mb-2" required>
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                    <option value="{{ $project->id }}">
                                        {{ $project->project_name }}
                                    </option>
                                    @endforeach
                                </select>

                                <select name="employee_id" class="form-control mb-2" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">
                                        {{ $emp->name }}
                                    </option>
                                    @endforeach
                                </select>

                                <select name="status" class="form-control mb-2">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>

                                <input type="date" name="due_date" class="form-control mb-2">

                                <textarea name="description" class="form-control mb-2"
                                    placeholder="Description"></textarea>

                                <button type="submit" class="btn btn-dark">
                                    Save
                                </button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

</x-app-layout>