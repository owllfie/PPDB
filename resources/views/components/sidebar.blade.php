<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700 duration-300 ease-in-out" aria-label="Sidebar">
   <div class="h-full flex flex-col px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
      <ul class="space-y-1.5 font-medium flex-1">

         @php
            $userRole = Auth::user()->roleRelation;
         @endphp

         @if($userRole && ($userRole->hasPermission('admin.users') || $userRole->hasPermission('admin.queue') || $userRole->hasPermission('admin.reports')))
            <li class="pt-3">
               <p class="px-2.5 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Management</p>
            </li>
            @if($userRole->hasPermission('admin.users'))
            <li>
               <a href="{{ route('admin.users') }}" class="flex items-center p-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.users') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                  <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 18" xmlns="http://www.w3.org/2000/svg">
                     <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                  </svg>
                  <span class="ml-3">Users</span>
               </a>
            </li>
            @endif
            @if($userRole->hasPermission('admin.queue'))
            <li>
               <a href="{{ route('admin.queue') }}" class="flex items-center p-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.queue') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                  <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                     <path d="M5 3a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5ZM5 11a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H5ZM11 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2V5ZM11 13a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-2Z"/>
                  </svg>
                  <span class="ml-3">Registration Queue</span>
               </a>
            </li>
            <li>
               <a href="{{ route('admin.tests') }}" class="flex items-center p-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.tests') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                  <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="M19 3H5a2 2 0 0 0-2 2v14l4-3h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-7 10H8v-2h4v2zm4-4H8V7h8v2z"/>
                  </svg>
                  <span class="ml-3">Test Review</span>
               </a>
            </li>
            @endif
            @if($userRole->hasPermission('admin.reports'))
            <li>
               <a href="{{ route('admin.reports') }}" class="flex items-center p-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.reports') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                  <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                  </svg>
                  <span class="ml-3">Reports</span>
               </a>
            </li>
            @endif
         @endif

         @if($userRole && ($userRole->hasPermission('admin.access') || $userRole->hasPermission('admin.logs') || $userRole->hasPermission('admin.settings') || $userRole->hasPermission('admin.backup')))
            <li class="pt-3">
               <p class="px-2.5 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Super Admin</p>
            </li>
            @if($userRole->hasPermission('admin.access'))
            <li>
               <a href="{{ route('admin.access') }}" class="flex items-center p-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.access') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                  <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                  </svg>
                  <span class="ml-3">Manage Access</span>
               </a>
            </li>
            @endif
            @if($userRole->hasPermission('admin.logs'))
            <li>
               <a href="{{ route('admin.logs') }}" class="flex items-center p-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.logs') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                  <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                  </svg>
                  <span class="ml-3">Activity Logs</span>
               </a>
            </li>
            @endif
            @if($userRole->hasPermission('admin.settings'))
            <li>
               <a href="{{ route('admin.settings') }}" class="flex items-center p-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.settings') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                  <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                     <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                  </svg>
                  <span class="ml-3">Web Settings</span>
               </a>
            </li>
            @endif
            @if($userRole->hasPermission('admin.backup'))
            <li>
               <a href="{{ route('admin.backup') }}" class="flex items-center p-2.5 rounded-lg transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="return confirm('Download database backup?')">
                  <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="M19.35 10.04A7.49 7.49 0 0 0 12 4C9.11 4 6.6 5.64 5.35 8.04A5.994 5.994 0 0 0 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/>
                  </svg>
                  <span class="ml-3">DB Backup</span>
               </a>
            </li>
            @endif
         @endif
      </ul>

      <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
         <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center w-full p-2.5 text-red-600 transition-colors rounded-lg group hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
               <svg class="flex-shrink-0 w-5 h-5" fill="none" viewBox="0 0 18 16" xmlns="http://www.w3.org/2000/svg">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3"/>
               </svg>
               <span class="ml-3">Sign Out</span>
            </button>
         </form>
      </div>
   </div>
</aside>
