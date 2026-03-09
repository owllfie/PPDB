@extends('app')

@section('title', 'Inbox')

@section('content')
<div class="px-4 pt-6 pb-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inbox</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Status updates from the school about your registration.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($messages as $message)
                <div class="px-6 py-5 {{ $message->read_at ? 'bg-white dark:bg-gray-800' : 'bg-indigo-50/70 dark:bg-indigo-900/10' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $message->subject }}</h2>
                                @if($message->status)
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full
                                        {{ $message->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                           ($message->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                                           ($message->status === 'uncertain' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' :
                                           'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400')) }}">
                                        {{ ucfirst($message->status) }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $message->message }}</p>
                            @if($message->action_url && $message->action_label)
                                <a href="{{ $message->action_url }}" class="inline-flex mt-4 items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                                    {{ $message->action_label }}
                                </a>
                            @endif
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                            {{ $message->created_at?->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    Belum ada pesan masuk.
                </div>
            @endforelse
        </div>

        @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
