@extends('app')

@section('title', 'Manage Access')

@section('content')
<div class="px-4 pt-6 pb-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Manage Access</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Configure feature access for each administrative role.</p>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-300 dark:border-green-800" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <form action="{{ route('admin.access') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search feature or slug..." 
                        data-live-search="true" data-target="#table-container"
                        class="block w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-xl bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500 transition-all" autocomplete="off">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">Search</button>
                <a href="{{ route('admin.access') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-all text-center">Reset</a>
            </div>
        </form>
    </div>

    <form action="{{ route('admin.access.update') }}" method="POST">
        @csrf
        <div id="table-container" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold border-r border-gray-200 dark:border-gray-600">
                                Feature / Page
                            </th>
                            @foreach($roles as $role)
                                <th scope="col" class="px-6 py-4 text-center">
                                    {{ $role->role }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white border-r border-gray-200 dark:border-gray-600">
                                    <div class="flex flex-col">
                                        <span class="font-semibold">{{ $permission->name }}</span>
                                        <span class="text-xs text-gray-400 font-mono">{{ $permission->slug }}</span>
                                    </div>
                                </th>
                                @foreach($roles as $role)
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" 
                                                   name="permissions[{{ $role->id_role }}][]" 
                                                   value="{{ $permission->id_permission }}"
                                                   {{ $role->hasPermission($permission->slug) ? 'checked' : '' }}
                                                   class="w-5 h-5 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 transition-all cursor-pointer">
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800 shadow-lg shadow-indigo-500/30 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                Save Access Changes
            </button>
        </div>
    </form>
</div>
@endsection
