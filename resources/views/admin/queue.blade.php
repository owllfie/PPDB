@extends('app')

@section('title', 'Registration Queue')

@section('content')
<div class="px-4 pt-6 pb-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Registration Queue</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Approve or reject incoming student registrations</p>
    </div>

    <div class="mb-4 flex justify-between items-center gap-4">
        <form action="{{ route('admin.queue') }}" method="GET" class="relative flex-1 max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search NISN or name..." 
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
                        <th class="px-6 py-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nisn', 'order' => request('sort') == 'nisn' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                NISN
                                @if(request('sort') == 'nisn')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_lengkap', 'order' => request('sort') == 'nama_lengkap' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                Nama Lengkap
                                @if(request('sort') == 'nama_lengkap')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-4">Jenis Kelamin</th>
                        <th class="px-6 py-4">Sekolah Asal</th>
                        <th class="px-6 py-4">No HP</th>
                        <th class="px-6 py-4">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => request('sort') == 'created_at' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                Tanggal
                                @if(request('sort', 'created_at') == 'created_at')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order', 'desc') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $reg)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $reg->nisn }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $reg->nama_lengkap }}</td>
                            <td class="px-6 py-4">{{ $reg->jenis_kelamin }}</td>
                            <td class="px-6 py-4">{{ $reg->sekolah_asal }}</td>
                            <td class="px-6 py-4">{{ $reg->no_hp }}</td>
                            <td class="px-6 py-4">{{ $reg->created_at?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="document.getElementById('detail-modal-{{ $reg->id_registrasi }}').classList.remove('hidden')" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                        Detail
                                    </button>
                                    <form action="{{ route('admin.queue.approve', $reg->id_registrasi) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 dark:text-green-400 dark:bg-green-900/30 dark:hover:bg-green-900/50 transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.queue.reject', $reg->id_registrasi) }}" method="POST" class="inline" onsubmit="return confirm('Tolak pendaftaran ini?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-900/50 transition-colors">
                                            Reject
                                        </button>
                                    </form>
                                </div>

                                <div id="detail-modal-{{ $reg->id_registrasi }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-2xl mx-4 max-h-[80vh] overflow-y-auto">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Pendaftaran</h3>
                                            <button onclick="this.closest('[id^=detail-modal]').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">NISN:</span> <span class="text-gray-900 dark:text-white">{{ $reg->nisn }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">NIK:</span> <span class="text-gray-900 dark:text-white">{{ $reg->nik }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Nama:</span> <span class="text-gray-900 dark:text-white">{{ $reg->nama_lengkap }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Email:</span> <span class="text-gray-900 dark:text-white">{{ $reg->email }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Tempat Lahir:</span> <span class="text-gray-900 dark:text-white">{{ $reg->tempat_lahir }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Tanggal Lahir:</span> <span class="text-gray-900 dark:text-white">{{ $reg->tanggal_lahir?->format('d M Y') }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin:</span> <span class="text-gray-900 dark:text-white">{{ $reg->jenis_kelamin }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Agama:</span> <span class="text-gray-900 dark:text-white">{{ $reg->agama }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Anak Ke:</span> <span class="text-gray-900 dark:text-white">{{ $reg->{'anak_ke-'} }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">No HP:</span> <span class="text-gray-900 dark:text-white">{{ $reg->no_hp }}</span></div>
                                            <div class="col-span-2"><span class="font-medium text-gray-700 dark:text-gray-300">Alamat:</span> <span class="text-gray-900 dark:text-white">{{ $reg->alamat_lengkap }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Nama Ayah:</span> <span class="text-gray-900 dark:text-white">{{ $reg->nama_ayah }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Nama Ibu:</span> <span class="text-gray-900 dark:text-white">{{ $reg->nama_ibu }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Pekerjaan Ayah:</span> <span class="text-gray-900 dark:text-white">{{ $reg->pekerjaan_ayah }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Pekerjaan Ibu:</span> <span class="text-gray-900 dark:text-white">{{ $reg->pekerjaan_ibu }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Sekolah Asal:</span> <span class="text-gray-900 dark:text-white">{{ $reg->sekolah_asal }}</span></div>
                                            <div><span class="font-medium text-gray-700 dark:text-gray-300">Nilai Rapor:</span> <span class="text-gray-900 dark:text-white">{{ $reg->nilai_rapor }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Tidak ada pendaftaran baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $registrations->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
