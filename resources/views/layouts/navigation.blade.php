<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    @use('App\Enums\RoleEnum')
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 d-flex items-center">
                    @if(activeMess())
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        @can('meals.view')
                            <x-nav-link :href="route('meals.index')" :active="request()->routeIs('meals.*')">
                                {{ __('Meals') }}
                            </x-nav-link>
                        @endcan
                        <x-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                            {{ __('Expenses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('deposits.index')" :active="request()->routeIs('deposits.*')">
                            {{ __('Deposits') }}
                        </x-nav-link>
                        @can('reports.view')
                            <x-nav-link :href="route('reports.all-months')" :active="request()->routeIs('reports.*')">
                                {{ __('Reports') }}
                            </x-nav-link>
                        @endcan
                        @can('members.view')
                            <x-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
                                {{ __('Members') }}
                            </x-nav-link>
                        @endcan
                        @can('months.view')
                            <x-nav-link :href="route('months.index')" :active="request()->routeIs('months.*')">
                                {{ __('Months') }}
                            </x-nav-link>
                        @endcan
                        @if(auth()->user() && auth()->user()->hasRole(RoleEnum::SUPERADMIN->value))
                            <x-nav-link :href="route('permissions.index')" :active="request()->routeIs('permissions.*')">
                                {{ __('Permissions') }}
                            </x-nav-link>
                        @endif
                    @else
                        <x-nav-link :href="route('mess.selection')" :active="request()->routeIs('mess.selection')">
                            {{ __('Select a Mess') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <!-- Pending Invitations Link -->
                    @role(['manager', 'superadmin'])
                        <a href="{{ route('mess.pending-invitations') }}" class="ms-4">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-warning rounded-md hover:bg-warning-dark">
                                <i class="fa-solid fa-envelope me-2"></i>
                                Pending
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-danger rounded-full">
                                    {{ pendingInvitationsCount() }}
                                </span>
                            </span>
                        </a>
                    @endrole

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Exit Mess for Superadmin -->
                            @if(isSuperAdminInMess())
                                <div class="px-4 py-2 bg-yellow-50 border-b border-yellow-200">
                                    <p class="text-xs font-semibold text-yellow-700 mb-2">
                                        <i class="fa-solid fa-crown me-1"></i> Superadmin Mode
                                    </p>
                                    <p class="text-xs text-yellow-600 mb-3">Viewing: <strong>{{ activeMess()->name }}</strong></p>
                                    <form method="POST" action="{{ route('mess.exit') }}" class="mb-0">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 rounded transition">
                                            <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Exit Mess
                                        </button>
                                    </form>
                                </div>
                                <div class="border-t border-gray-100"></div>
                            @endif

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if(activeMess())
                                <x-dropdown-link :href="route('mess.profile', activeMess())">
                                    <i class="fa-solid fa-cog me-1"></i> {{ __('Mess Settings') }}
                                </x-dropdown-link>
                                <div class="border-t border-gray-100"></div>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div style="text-align: right;">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary">
                            {{ __('Login') }}
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(activeMess())
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                @can('meals.viewAny')
                    <x-responsive-nav-link :href="route('meals.index')" :active="request()->routeIs('meals.*')">
                        {{ __('Meals') }}
                    </x-responsive-nav-link>
                @endcan
                <x-responsive-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                    {{ __('Expenses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('deposits.index')" :active="request()->routeIs('deposits.*')">
                    {{ __('Deposits') }}
                </x-responsive-nav-link>
                @can('reports.view')
                    <x-responsive-nav-link :href="route('reports.all-months')" :active="request()->routeIs('reports.*')">
                        {{ __('Reports') }}
                    </x-responsive-nav-link>
                @endcan
                @can('members.view')
                    <x-responsive-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
                        {{ __('Members') }}
                    </x-responsive-nav-link>
                @endcan
                @can('months.view')
                    <x-responsive-nav-link :href="route('months.index')" :active="request()->routeIs('months.*')">
                        {{ __('Months') }}
                    </x-responsive-nav-link>
                @endcan
                @if(auth()->user() && auth()->user()->hasRole(RoleEnum::SUPERADMIN->value))
                    <x-responsive-nav-link :href="route('permissions.index')" :active="request()->routeIs('permissions.*')">
                        {{ __('Permissions') }}
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('mess.selection')" :active="request()->routeIs('mess.selection')">
                    {{ __('Select a Mess') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Exit Mess for Superadmin -->
                    @if(isSuperAdminInMess())
                        <div class="px-4 py-3 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-xs font-semibold text-yellow-700 mb-2">
                                <i class="fa-solid fa-crown me-1"></i> Superadmin Mode
                            </p>
                            <p class="text-xs text-yellow-600 mb-3">Viewing: <strong>{{ activeMess()->name }}</strong></p>
                            <form method="POST" action="{{ route('mess.exit') }}" class="mb-0">
                                @csrf
                                <button type="submit" class="w-full text-left px-2 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 rounded transition font-medium">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Exit Mess
                                </button>
                            </form>
                        </div>
                    @endif
                    
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    @if(activeMess())
                        <x-responsive-nav-link :href="route('mess.profile', activeMess())">
                            <i class="fa-solid fa-cog me-1"></i> {{ __('Mess Settings') }}
                        </x-responsive-nav-link>
                        <div class="border-t border-gray-200"></div>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4">
                    <p class="text-sm text-gray-600">{{ __('Not logged in') }}</p>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Login') }}
                    </x-responsive-nav-link>

                </div>
            @endauth
        </div>
    </div>
</nav>
