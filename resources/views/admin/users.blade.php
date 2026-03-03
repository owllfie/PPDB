@extends('app')

@section('title', 'User Management')

@section('content')
<div class="lg:px-2 pt-2 pb-8">

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">User Management</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage active accounts, restore deleted users, and track change history</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Tabs Header -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
            <li class="mr-2">
                <button onclick="switchTab('now')" id="tab-now" class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg transition-all group tab-btn active-tab" data-tab="now">
                    <svg class="w-4 h-4 mr-2 text-gray-400 group-[.active-tab]:text-indigo-600 dark:group-[.active-tab]:text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    Active Users (Now)
                </button>
            </li>
            <li class="mr-2">
                <button onclick="switchTab('deleted')" id="tab-deleted" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition-all group tab-btn shadow-sm" data-tab="deleted">
                    <svg class="w-4 h-4 mr-2 text-gray-400 group-[.active-tab]:text-rose-600 dark:group-[.active-tab]:text-rose-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    Trash Bin (Deleted)
                </button>
            </li>
            <li class="mr-2">
                <button onclick="switchTab('history')" id="tab-history" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition-all group tab-btn shadow-sm" data-tab="history">
                    <svg class="w-4 h-4 mr-2 text-gray-400 group-[.active-tab]:text-amber-600 dark:group-[.active-tab]:text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                    Audit Log (History)
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Contents -->
    <div id="tab-content-now" class="tab-pane active">
        <div class="mb-4 flex justify-between items-center gap-4">
            <form action="{{ route('admin.users') }}" method="GET" class="relative flex-1 max-w-sm">
                <input type="hidden" name="tab" value="now">
                <input type="text" name="search_now" value="{{ request('search_now') }}" placeholder="Search active users..." 
                    data-live-search="true" data-target="#container-now"
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>
        </div>
        <div id="container-now">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <!-- ... rest of table ... -->
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort_now' => 'username', 'order_now' => request('sort_now') == 'username' && request('order_now') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                    Username
                                    @if(request('sort_now') == 'username')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order_now') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort_now' => 'email', 'order_now' => request('sort_now') == 'email' && request('order_now') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                    Email
                                    @if(request('sort_now') == 'email')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order_now') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort_now' => 'role', 'order_now' => request('sort_now') == 'role' && request('order_now') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                    Role
                                    @if(request('sort_now') == 'role')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order_now') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4">Verified</th>
                            <th class="px-6 py-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort_now' => 'created_at', 'order_now' => request('sort_now') == 'created_at' && request('order_now') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                                    Created
                                    @if(request('sort_now', 'created_at') == 'created_at')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order_now', 'desc') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $users->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $user->username }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                        {{ ($user->roleRelation && $user->roleRelation->hasPermission('admin.access')) ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' :
                                           ((int)$user->role >= 2 ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' :
                                           'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300') }}">
                                        {{ $user->roleRelation->role ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_verified)
                                        <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            Verified
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs">{{ $user->created_at?->format('d M Y') ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="document.getElementById('role-modal-{{ $user->id_user }}').classList.remove('hidden')" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors dark:text-amber-400 dark:hover:bg-amber-900/30" title="Change Role">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </button>
                                        <button onclick="document.getElementById('reset-modal-{{ $user->id_user }}').classList.remove('hidden')" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors dark:text-indigo-400 dark:hover:bg-indigo-900/30" title="Reset Password">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                        </button>
                                        @if($user->id_user !== Auth::id())
                                            <form action="{{ route('admin.users.delete', $user->id_user) }}" method="POST" onsubmit="return confirm('Pindahkan user ini ke tempat sampah?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors dark:text-rose-400 dark:hover:bg-rose-900/30" title="Delete (Move to Trash)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <!-- Modals (Hidden by default) -->
                                    <div id="role-modal-{{ $user->id_user }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm">
                                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4 transform transition-all">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Change User Role</h3>
                                            <p class="text-sm text-gray-500 mb-6">Updating privileges for <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $user->username }}</span></p>
                                            <form action="{{ route('admin.users.role', $user->id_user) }}" method="POST">
                                                @csrf
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Role</label>
                                                        <select name="role" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                            @foreach($roles as $role)
                                                                @if(!$role->hasPermission('admin.access'))
                                                                    <option value="{{ $role->id_role }}" {{ (int)$user->role === (int)$role->id_role ? 'selected' : '' }}>
                                                                        {{ $role->role }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="flex justify-end gap-3 mt-8">
                                                    <button type="button" onclick="this.closest('[id^=role-modal]').classList.add('hidden')" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancel</button>
                                                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <div id="reset-modal-{{ $user->id_user }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm">
                                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4 transform transition-all">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Reset Password</h3>
                                            <p class="text-sm text-gray-500 mb-6">For user: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $user->username }}</span></p>
                                            <form action="{{ route('admin.users.reset', $user->id_user) }}" method="POST">
                                                @csrf
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                                                        <input type="password" name="password" required minlength="8" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                                                        <input type="password" name="password_confirmation" required minlength="8" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-end gap-3 mt-8">
                                                    <button type="button" onclick="this.closest('[id^=reset-modal]').classList.add('hidden')" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancel</button>
                                                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/30">Reset Now</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        <p class="text-gray-500 dark:text-gray-400">No active users found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $users->appends(request()->except('users_page'))->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="tab-content-deleted" class="tab-pane hidden">
        <div class="mb-4 flex justify-between items-center gap-4">
            <form action="{{ route('admin.users') }}" method="GET" class="relative flex-1 max-w-sm">
                <input type="hidden" name="tab" value="deleted">
                <input type="text" name="search_deleted" value="{{ request('search_deleted') }}" placeholder="Search trash bin..." 
                    data-live-search="true" data-target="#container-deleted"
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-rose-500 transition-all">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>
        </div>
        <div id="container-deleted">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-rose-50 dark:bg-rose-900/10 dark:text-rose-400">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort_deleted' => 'username', 'order_deleted' => request('sort_deleted') == 'username' && request('order_deleted') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-rose-600 transition-colors">
                                    Username
                                    @if(request('sort_deleted') == 'username')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order_deleted') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort_deleted' => 'email', 'order_deleted' => request('sort_deleted') == 'email' && request('order_deleted') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-rose-600 transition-colors">
                                    Email
                                    @if(request('sort_deleted') == 'email')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order_deleted') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort_deleted' => 'deleted_at', 'order_deleted' => request('sort_deleted') == 'deleted_at' && request('order_deleted') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-rose-600 transition-colors">
                                    Deleted At
                                    @if(request('sort_deleted', 'deleted_at') == 'deleted_at')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order_deleted', 'desc') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($deletedUsers as $user)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-rose-50/30 dark:hover:bg-rose-900/10 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $deletedUsers->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $user->username }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-xs text-rose-600 dark:text-rose-400">{{ $user->deleted_at?->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <form action="{{ route('admin.users.restore', $user->id_user) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-900/30 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                Restore
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.force_delete', $user->id_user) }}" method="POST" onsubmit="return confirm('PERINGATAN: User akan dihapus secara permanen! Lanjutkan?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 rounded-lg hover:bg-rose-100 dark:text-rose-400 dark:bg-rose-900/30 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Purge
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <p class="text-gray-500 dark:text-gray-400">Trash bin is empty.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $deletedUsers->appends(request()->except('deleted_page'))->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="tab-content-history" class="tab-pane hidden">
        <div class="mb-4 flex justify-between items-center gap-4">
            <form action="{{ route('admin.users') }}" method="GET" class="relative flex-1 max-w-sm">
                <input type="hidden" name="tab" value="history">
                <input type="text" name="search_history" value="{{ request('search_history') }}" placeholder="Search audit logs..." 
                    data-live-search="true" data-target="#container-history"
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-amber-500 transition-all">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>
        </div>
        <div id="container-history">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-amber-50 dark:bg-amber-900/10 dark:text-amber-400">
                        <tr>
                            <th class="px-6 py-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort_history' => 'created_at', 'order_history' => request('sort_history') == 'created_at' && request('order_history') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-amber-600 transition-colors">
                                    Time
                                    @if(request('sort_history', 'created_at') == 'created_at')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order_history', 'desc') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">
                                <a href="{{ request()->fullUrlWithQuery(['sort_history' => 'field', 'order_history' => request('sort_history') == 'field' && request('order_history') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-amber-600 transition-colors">
                                    Field
                                    @if(request('sort_history') == 'field')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('order_history') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-4 text-center">Change</th>
                            <th class="px-6 py-4">Changed By</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($history as $item)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-amber-50/20 dark:hover:bg-amber-900/10 transition-colors">
                                <td class="px-6 py-4 text-xs">{{ $item->created_at->format('d M H:i:s') }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $item->user->username ?? 'Deleted User' }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $item->user->email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4"><span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-[10px] uppercase font-bold">{{ $item->field }}</span></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2 text-xs">
                                        <span class="text-rose-500 line-through opacity-70">{{ $item->old_value }}</span>
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        <span class="text-emerald-500 font-bold">{{ $item->new_value }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-indigo-600 dark:text-indigo-400">{{ $item->admin->username ?? 'System' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.history.revert', $item->id_history) }}" method="POST" onsubmit="return confirm('Kembalikan data ke nilai sebelumnya?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-amber-700 bg-amber-100 rounded-lg hover:bg-amber-200 dark:text-amber-300 dark:bg-amber-900/40 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            Revert
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-gray-500 dark:text-gray-400">No history records found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $history->appends(request()->except('history_page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId) {
        // Hide all panes
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
            pane.classList.remove('active');
        });
        
        // Remove active styling from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active-tab', 'border-indigo-600', 'text-indigo-600', 'border-rose-600', 'text-rose-600', 'border-amber-600', 'text-amber-600', 'dark:border-indigo-400', 'dark:text-indigo-400', 'dark:border-rose-400', 'dark:text-rose-400', 'dark:border-amber-400', 'dark:text-amber-400');
            btn.classList.add('border-transparent');
        });

        // Show target pane
        const targetPane = document.getElementById('tab-content-' + tabId);
        targetPane.classList.remove('hidden');
        targetPane.classList.add('active');

        // Style the active button
        const activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.classList.add('active-tab');
        activeBtn.classList.remove('border-transparent');

        // Specific colors per tab
        if (tabId === 'now') {
            activeBtn.classList.add('border-indigo-600', 'text-indigo-600', 'dark:border-indigo-400', 'dark:text-indigo-400');
        } else if (tabId === 'deleted') {
            activeBtn.classList.add('border-rose-600', 'text-rose-600', 'dark:border-rose-400', 'dark:text-rose-400');
        } else if (tabId === 'history') {
            activeBtn.classList.add('border-amber-600', 'text-amber-600', 'dark:border-amber-400', 'dark:text-amber-400');
        }

        // Store active tab in localStorage
        localStorage.setItem('activeUserTab', tabId);
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = localStorage.getItem('activeUserTab') || 'now';

        if (urlParams.has('deleted_page')) {
            activeTab = 'deleted';
        } else if (urlParams.has('history_page')) {
            activeTab = 'history';
        } else if (urlParams.has('users_page')) {
            activeTab = 'now';
        }

        switchTab(activeTab);
    });
</script>

<style>
    .active-tab {
        @apply shadow-sm;
    }
</style>

@endsection
