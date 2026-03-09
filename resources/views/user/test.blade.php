@extends('app')

@section('title', 'Tes Masuk')

@section('content')
<div class="px-4 pt-6 pb-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tes Seleksi</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $test->test_type === 'follow_up' ? 'Tes lanjutan untuk keputusan uncertain.' : 'Tes kemampuan dasar dan tes minat bakat untuk calon siswa yang lolos verifikasi berkas.' }}
        </p>
    </div>

    @if($test->status === 'submitted')
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-5 text-sm text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-300">
            Tes ini sudah Anda kirim. Silakan tunggu keputusan sekolah melalui inbox.
        </div>
    @else
        <form action="{{ route('user.test.submit', $test->token) }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tes Kemampuan Dasar</h2>
                <div class="space-y-5">
                    @foreach($questionSets['basic'] as $index => $question)
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $loop->iteration }}. {{ $question['question'] }}</p>
                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($question['options'] as $optionKey => $optionLabel)
                                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 cursor-pointer">
                                        <input type="radio" name="basic_answers[{{ $index }}]" value="{{ $optionKey }}" class="text-indigo-600" required>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $optionKey }}. {{ $optionLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tes Minat dan Bakat</h2>
                <div class="space-y-5">
                    @foreach($questionSets['interest'] as $index => $question)
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $loop->iteration }}. {{ $question['question'] }}</p>
                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($question['options'] as $optionKey => $optionLabel)
                                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 cursor-pointer">
                                        <input type="radio" name="interest_answers[{{ $index }}]" value="{{ $optionKey }}" class="text-indigo-600" required>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $optionKey }}. {{ $optionLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition-colors">
                Kirim Tes
            </button>
        </form>
    @endif
</div>
@endsection
