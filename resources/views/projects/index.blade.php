<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Projects') }}
        </h2>
    </x-slot>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold">Projects List</h4>

                @if(in_array(auth()->user()->role, ['admin','manager']))
                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                    + Add Project
                </button>
                @endif
            </div>

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white shadow-sm rounded overflow-auto">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Project Name</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Manager Assigned</th>
                            <th>Assigned Employees</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($projects as $key => $project)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $project->project_name }}</td>
                            <td>{{ $project->description }}</td>
                            <td>{{ $project->due_date }}</td>
                            <td>{{ $project->manager->name ?? '-' }}</td>
                            <td>
                                @foreach($project->employees as $emp)
                                <span class="badge bg-secondary">
                                    {{ $emp->name }}
                                </span>
                                @endforeach
                            </td>
                            <td class="d-flex gap-2">

                                <button class="btn btn-secondary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewProjectModal{{ $project->id }}">
                                    View
                                </button>

                                @if(in_array(auth()->user()->role, ['admin','manager']))
                                <button class="btn btn-dark btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editProjectModal{{ $project->id }}">
                                    Edit
                                </button>
                                @endif

                                @if(auth()->user()->role == 'admin')
                                <form action="{{ route('projects.destroy',$project->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                                @endif

                            </td>
                        </tr>

                        <div class="modal fade" id="viewProjectModal{{ $project->id }}">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>Project Details</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Name:</strong> {{ $project->project_name }}</p>
                                        <p><strong>Description:</strong> {{ $project->description }}</p>
                                        <p><strong>Due Date:</strong> {{ $project->due_date }}</p>
                                        <p><strong>Manager:</strong> {{ $project->manager->name ?? '-' }}</p>

                                        <p><strong>Employees:</strong></p>
                                        <ul>
                                            @foreach($project->employees as $emp)
                                            <li>{{ $emp->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(in_array(auth()->user()->role, ['admin','manager']))
                        <div class="modal fade" id="editProjectModal{{ $project->id }}">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>Edit Project</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <form action="{{ route('projects.update',$project->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-3">
                                                <label>Project Name</label>
                                                <input type="text" name="project_name"
                                                    class="form-control"
                                                    value="{{ $project->project_name }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label>Description</label>
                                                <textarea name="description"
                                                    class="form-control">{{ $project->description }}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label>Due Date</label>
                                                <input type="date" name="due_date"
                                                    class="form-control"
                                                    value="{{ $project->due_date }}">
                                            </div>

                                            <div class="mb-3">
                                                <label>Manager</label>
                                                <select name="manager_id" class="form-control">
                                                    @foreach($managers as $manager)
                                                    <option value="{{ $manager->id }}"
                                                        {{ $project->manager_id == $manager->id ? 'selected':'' }}>
                                                        {{ $manager->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label>Assign Employees</label>
                                                <select name="employees[]"
                                                    class="form-control selectpicker"
                                                    multiple
                                                    data-live-search="true"
                                                    title="Select Employees">

                                                    @foreach($employees as $emp)
                                                    <option value="{{ $emp->id }}"
                                                        {{ $project->employees->contains('id', $emp->id) ? 'selected' : '' }}>
                                                        {{ $emp->name }}
                                                    </option>
                                                    @endforeach

                                                </select>
                                            </div>

                                            <div class="text-end">
                                                <button type="submit" class="btn btn-dark">
                                                    Update
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                No Projects Found
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            @if(in_array(auth()->user()->role, ['admin','manager']))
            <div class="modal fade" id="addProjectModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5>Add Project</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <form action="{{ route('projects.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label>Project Name</label>
                                    <input type="text" name="project_name"
                                        class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea name="description"
                                        class="form-control"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label>Due Date</label>
                                    <input type="date" name="due_date"
                                        class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label>Manager</label>
                                    <select name="manager_id" class="form-control">
                                        @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}">
                                            {{ $manager->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label>Assign Employees</label>
                                    <select name="employees[]"
                                        class="form-control selectpicker"
                                        multiple
                                        data-live-search="true"
                                        title="Select Employees">

                                        @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->name }}
                                        </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-dark">
                                        Save
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

</x-app-layout>

<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker();
    });
</script>