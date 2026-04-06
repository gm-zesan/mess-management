<nav class="sticky top-0 z-50 bg-white border-b border-gray-200">
    @use('App\Enums\RoleEnum')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                    <x-application-logo class="block h-8 w-auto fill-current text-sky-600 transition-transform group-hover:scale-110" />
                    <span class="hidden sm:inline font-bold text-gray-900">Mess Management</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-1">
                @if(activeMess() || isSuperAdminInMess())
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} transition-colors">
                        Dashboard
                    </a>

                    @if(activeMonth())
                        @can('meals.view')
                            <a href="{{ route('meals.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('meals.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} transition-colors">
                                Meals
                            </a>
                        @endcan
                        @can('expenses.view')
                            <a href="{{ route('expenses.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('expenses.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} transition-colors">
                                Expenses
                            </a>
                        @endcan
                        @can('deposits.view')
                            <a href="{{ route('deposits.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('deposits.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} transition-colors">
                                Deposits
                            </a>
                        @endcan
                    @endif
                    @can('reports.view')
                        <a href="{{ route('reports.all-months') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('reports.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} transition-colors">
                            Reports
                        </a>
                    @endcan
                    @can('members.view')
                        <a href="{{ route('members.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('members.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} transition-colors">
                            Members
                        </a>
                    @endcan
                    @can('months.view')
                        <a href="{{ route('months.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('months.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} transition-colors">
                            Months
                        </a>
                    @endcan
                @endif
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center gap-3">
                @auth
                    <!-- Pending Invitations Badge -->
                    @role([RoleEnum::MANAGER->value, RoleEnum::SUPERADMIN->value])
                        <a href="{{ route('mess.pending-invitations') }}" class="relative inline-flex items-center gap-2 px-3 py-2 rounded-md bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            <span class="hidden sm:inline">Pending</span>
                            @if(pendingInvitationsCount() > 0)
                                <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                    {{ pendingInvitationsCount() }}
                                </span>
                            @endif
                        </a>
                    @endrole

                    <!-- User Dropdown -->
                    <div class="relative group">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="hidden sm:inline text-sm font-medium text-gray-900">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-0 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150">
                            <!-- Superadmin Mode Badge -->
                            @if(isSuperAdminInMess())
                                <div class="px-4 py-3 bg-yellow-50 border-b border-yellow-200 rounded-t-lg">
                                    <p class="text-xs font-semibold text-yellow-700 mb-1 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Superadmin Mode
                                    </p>
                                    <p class="text-xs text-yellow-600 mb-3">Viewing: <strong>{{ activeMess()->name }}</strong></p>
                                    <form method="POST" action="{{ route('mess.exit') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-2 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 rounded transition flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Exit Mess
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 {{ isSuperAdminInMess() ? '' : 'border-t border-gray-200' }} {{ isSuperAdminInMess() ? 'rounded-t-none' : 'rounded-t-lg' }} transition-colors">
                                <svg class="inline w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                Profile
                            </a>

                            @if(activeMess())
                                <a href="{{ route('mess.profile', activeMess()) }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="inline w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Mess Settings
                                </a>
                                @role(RoleEnum::MANAGER->value)
                                <a href="{{ route('months.create-current') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="inline w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h-6m0 0H6"></path>
                                    </svg>
                                    Create New Month
                                </a>
                                @endrole
                                <div class="border-t border-gray-200"></div>
                            @endif

                            @if(auth()->user() && auth()->user()->hasRole(RoleEnum::SUPERADMIN->value))
                                <a href="{{ route('permissions.index') }}" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="inline w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Permissions
                                </a>
                                <div class="border-t border-gray-200"></div>
                            @endif

                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-b-lg transition-colors">
                                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-md text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 transition-colors">
                        Sign In
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button @click="open = !open" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500">
                <svg class="h-6 w-6" :class="{ 'hidden': open, 'block': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg class="h-6 w-6" :class="{ 'block': open, 'hidden': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div x-show="open" class="md:hidden border-t border-gray-200 bg-gray-50">
        <div class="px-2 pt-2 pb-3 space-y-1">
            @if(activeMess())
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} transition-colors">
                    Dashboard
                </a>
                @can('meals.viewAny')
                    <a href="{{ route('meals.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('meals.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} transition-colors">
                        Meals
                    </a>
                @endcan
                <a href="{{ route('expenses.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('expenses.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} transition-colors">
                    Expenses
                </a>
                <a href="{{ route('deposits.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('deposits.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} transition-colors">
                    Deposits
                </a>
                @can('reports.view')
                    <a href="{{ route('reports.all-months') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('reports.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} transition-colors">
                        Reports
                    </a>
                @endcan
                @can('members.view')
                    <a href="{{ route('members.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('members.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} transition-colors">
                        Members
                    </a>
                @endcan
                @can('months.view')
                    <a href="{{ route('months.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('months.*') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} transition-colors">
                        Months
                    </a>
                @endcan
            @else
                <a href="{{ route('mess.selection') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('mess.selection') ? 'text-sky-600 bg-sky-50' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} transition-colors">
                    Select a Mess
                </a>
            @endif
        </div>

        <!-- Mobile Settings -->
        <div class="border-t border-gray-200 px-2 py-3 space-y-1">
            @auth
                <div class="px-3 py-2 text-sm font-medium text-gray-700">
                    {{ Auth::user()->name }}<br>
                    <span class="text-xs text-gray-500">{{ Auth::user()->email }}</span>
                </div>

                @if(isSuperAdminInMess())
                    <div class="px-3 py-2 bg-yellow-50 rounded-md border border-yellow-200 text-xs">
                        <p class="font-semibold text-yellow-700 mb-2">Superadmin Mode: {{ activeMess()->name }}</p>
                        <form method="POST" action="{{ route('mess.exit') }}" class="m-0">
                            @csrf
                            <button type="submit" class="text-red-600 font-medium hover:underline">Exit Mess</button>
                        </form>
                    </div>
                @endif

                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-white transition-colors">
                    Profile
                </a>

                @if(activeMess())
                    <a href="{{ route('mess.profile', activeMess()) }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-white transition-colors">
                        Mess Settings
                    </a>
                @endif

                @if(auth()->user() && auth()->user()->hasRole(RoleEnum::SUPERADMIN->value))
                    <a href="{{ route('permissions.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-white transition-colors">
                        Permissions
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                        Log Out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-sm font-semibold text-sky-600 hover:bg-sky-50 transition-colors">
                    Sign In
                </a>
            @endauth
        </div>
    </div>
</nav>
