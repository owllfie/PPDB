@extends('app')

@section('title', 'Test Review')

@section('content')
<div class="px-4 pt-6 pb-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Test Review</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Seleksi hasil tes calon siswa dan tentukan lulus, uncertain, atau gagal.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">NISN</th>
                        <th class="px-6 py-4">Jenis Tes</th>
                        <th class="px-6 py-4">Skor Dasar</th>
                        <th class="px-6 py-4">Skor Minat</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Tahap</th>
                        <th class="px-6 py-4 text-center">Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tests as $test)
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $test->nama_lengkap ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">{{ $test->nisn }}</td>
                            <td class="px-6 py-4">{{ $test->test_type === 'follow_up' ? 'Tes Lanjutan' : 'Tes Awal' }}</td>
                            <td class="px-6 py-4">{{ $test->basic_score }}</td>
                            <td class="px-6 py-4">{{ $test->interest_score }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $test->total_score }}</td>
                            <td class="px-6 py-4">{{ str_replace('_', ' ', $test->current_stage) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('admin.tests.pass', $test->id_registrasi) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 dark:text-green-400 dark:bg-green-900/30">
                                            Passed
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.tests.uncertain', $test->id_registrasi) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 rounded-lg hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-900/30">
                                            Uncertain
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.tests.fail', $test->id_registrasi) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:text-red-400 dark:bg-red-900/30">
                                            Failed
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">Belum ada hasil tes yang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $tests->links() }}
        </div>
    </div>
</div>
@endsection
