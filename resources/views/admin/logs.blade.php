@extends('app')

@section('title', 'Activity Logs')

@section('content')
<div class="px-4 pt-6 pb-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Activity Logs</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">System audit trail for all administrative actions</p>
    </div>

    <div class="mb-4 flex justify-between items-center gap-4">
        <form action="{{ route('admin.logs') }}" method="GET" class="relative flex-1 max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user or action..." 
                data-live-search="true" data-target="#table-container"
                class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
            <div class="absolute left-3 top-2.5 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if(request('order')) <input type="hidden" name="order" value="{{ request('order') }}"> @endif
        </form>
    </div>

    <div id="table-container">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'action', 'order' => request('sort') == 'action' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                Action
                                @if(request('sort') == 'action')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-4">IP Address</th>
                        <th class="px-6 py-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => request('sort') == 'created_at' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                Timestamp
                                @if(request('sort', 'created_at') == 'created_at')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order', 'desc') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                @endif
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $logs->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900 dark:text-white">{{ optional($log->user)->nama_lengkap ?? optional($log->user)->email ?? 'Deleted' }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $log->action }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-mono bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-300">{{ $log->ip_address ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $log->created_at?->format('d M Y H:i:s') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No activity logs recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
