<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 bg-white border-b border-gray-200">

                    <h3 class="text-lg font-semibold mb-2">
                        Welcome, {{ auth()->user()->name }}
                    </h3>

                    <p class="mb-4 text-gray-600">
                        Role: <span class="font-medium capitalize">
                            {{ auth()->user()->role }}
                        </span>
                    </p>

                    <div class="flex flex-wrap gap-4">

                        {{-- Admin & Manager --}}
                        @if(in_array(auth()->user()->role, ['admin', 'manager']))
                        <a href="{{ route('projects.index') }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Manage Projects
                        </a>

                        <a href="{{ route('tasks.index') }}"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Manage Tasks
                        </a>
                        @endif

                        {{-- Employee --}}
                        @if(auth()->user()->role == 'employee')
                        <a href="{{ route('tasks.index') }}"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            View My Tasks
                        </a>
                        @endif

                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>