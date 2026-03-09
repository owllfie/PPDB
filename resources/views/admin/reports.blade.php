@extends('app')

@section('title', 'Registration Reports')

@section('content')
<div class="px-4 pt-6 pb-8 main-content-container">

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Registration Reports</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Detailed list of student registrations and admissions</p>
        </div>
        <div class="flex items-center flex-wrap gap-2">
            <button onclick="window.print()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="{{ route('admin.reports.pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'search' => $search, 'sort' => $sort, 'order' => $order]) }}" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all flex items-center gap-2 shadow-lg shadow-red-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
            <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate, 'search' => $search, 'sort' => $sort, 'order' => $order]) }}" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all flex items-center gap-2 shadow-lg shadow-green-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-8 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 print:hidden">
        <form action="{{ route('admin.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Name</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Enter name..." 
                    data-live-search="true" data-target="#table-container"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                    data-live-search="true" data-target="#table-container"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                    data-live-search="true" data-target="#table-container"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all">Filter</button>
                <a href="{{ route('admin.reports') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-all text-center flex-1">Reset</a>
            </div>
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if(request('order')) <input type="hidden" name="order" value="{{ request('order') }}"> @endif
        </form>
    </div>

    <!-- Data Table -->
    <div id="table-container">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden print:shadow-none print:border-none">
        <div class="p-6 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Summary of Registrations</h3>
            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 text-sm font-bold rounded-full">
                Total: {{ $reportData['total'] }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-left">No</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_lengkap', 'order' => request('sort') == 'nama_lengkap' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                Nama Lengkap
                                @if(request('sort') == 'nama_lengkap')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => request('sort') == 'created_at' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                Tanggal Daftar
                                @if(request('sort', 'created_at') == 'created_at')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order', 'desc') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'order' => request('sort') == 'status' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                Status
                                @if(request('sort') == 'status')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                @endif
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($reportData['raw_data'] as $reg)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">{{ $reportData['raw_data']->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-bold">{{ $reg->nama_lengkap }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-medium">
                                {{ $reg->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 text-xs font-bold rounded-full
                                    {{ $reg->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 
                                       ($reg->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                                       ($reg->status === 'uncertain' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' :
                                       'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400')) }}">
                                    {{ ucfirst($reg->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 font-medium italic">
                                No registration records found for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $reportData['raw_data']->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>

<style>
    @media print {
        @page { size: portrait; margin: 2cm; }
        
        /* Hide all global navigation and sidebars */
        aside, nav, #logo-sidebar, .fixed, .print\:hidden {
            display: none !important;
        }

        /* Essential Reset for the content area */
        body { 
            background-color: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Specifically target the container to remove the sidebar offset */
        .sm\:ml-64 { 
            margin-left: 0 !important; 
        }

        /* Ensure the main content container is centered and full width */
        .main-content-container {
            margin: 0 auto !important;
            padding: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            display: block !important;
        }

        /* Table styling for print */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        
        th, td {
            border-bottom: 1px solid #ddd !important;
            padding: 10px !important;
        }

        /* Prevent background colors being stripped if user has it enabled, 
           but generally keep it clean for ink usage */
        .rounded-3xl { border: 1px solid #eee !important; border-radius: 0 !important; }
        .dark\:text-white { color: black !important; }
        .dark\:text-gray-400 { color: #555 !important; }
    }
</style>
@endsection
