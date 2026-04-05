<x-guest-layout>
    <!-- Heading -->
    <div class="text-center mb-12">
        <h1 class="font-[family-name:var(--font-sora)] text-2xl font-bold text-gray-900 mb-1">Create your account</h1>
        <p class="text-sm font-light text-gray-600">Start managing your mess and members today</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <!-- Register Form -->
    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
        @csrf

        <!-- Name -->
        <div class="flex flex-col gap-1">
            <label for="name" class="text-xs font-semibold uppercase tracking-wider text-gray-900">Full name</label>
            <div class="relative flex items-center">
                <svg class="absolute left-3.5 z-10 h-5 w-5 text-gray-500 pointer-events-none flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a5 5 0 100-10 5 5 0 000 10zM2 18a8 8 0 0116 0H2z"></path>
                </svg>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full rounded-md border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm font-normal text-gray-900 placeholder-gray-400 transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 {{ $errors->has('name') ? 'border-red-500' : '' }}"
                    placeholder="Your full name"
                />
            </div>
            <x-input-error :messages="$errors->get('name')" class="text-xs font-medium text-red-500 mt-1" />
        </div>

        <!-- Email Address -->
        <div class="flex flex-col gap-1">
            <label for="email" class="text-xs font-semibold uppercase tracking-wider text-gray-900">Email Address</label>
            <div class="relative flex items-center">
                <svg class="absolute left-3.5 z-10 h-5 w-5 text-gray-500 pointer-events-none flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                </svg>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                    class="w-full rounded-md border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm font-normal text-gray-900 placeholder-gray-400 transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 {{ $errors->has('email') ? 'border-red-500' : '' }}"
                    placeholder="you@company.com"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="text-xs font-medium text-red-500 mt-1" />
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-1">
            <label for="password" class="text-xs font-semibold uppercase tracking-wider text-gray-900">Password</label>
            <div class="relative flex items-center">
                <svg class="absolute left-3.5 z-10 h-5 w-5 text-gray-500 pointer-events-none flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                </svg>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-md border border-gray-300 bg-white py-2.5 pl-10 pr-16 text-sm font-normal text-gray-900 placeholder-gray-400 transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 {{ $errors->has('password') ? 'border-red-500' : '' }}"
                    placeholder="••••••••"
                />
                <button type="button" class="absolute right-3.5 bg-none border-none cursor-pointer px-2 py-1 text-xs font-semibold text-sky-600 transition-colors hover:text-sky-500 active:text-sky-700" onclick="togglePassword()" data-toggle="password">
                    <span class="toggle-text">Show</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="text-xs font-medium text-red-500 mt-1" />
        </div>

        <!-- Confirm Password -->
        <div class="flex flex-col gap-1">
            <label for="password_confirmation" class="text-xs font-semibold uppercase tracking-wider text-gray-900">Confirm Password</label>
            <div class="relative flex items-center">
                <svg class="absolute left-3.5 z-10 h-5 w-5 text-gray-500 pointer-events-none flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a5 5 0 100-10 5 5 0 000 10zM2 18a8 8 0 0116 0H2z"></path>
                </svg>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-md border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm font-normal text-gray-900 placeholder-gray-400 transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 {{ $errors->has('password_confirmation') ? 'border-red-500' : '' }}"
                    placeholder="••••••••"
                />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="text-xs font-medium text-red-500 mt-1" />
        </div>

        <!-- Create Account Button -->
        <button type="submit" class="w-full rounded-md bg-sky-600 px-4 py-2.5 font-[family-name:var(--font-sora)] text-sm font-semibold uppercase tracking-wider text-white transition-all hover:bg-sky-700 active:scale-98 focus:outline-none focus:ring-2 focus:ring-sky-100 mt-1 mb-4">
            Create Account
        </button>
    </form>

    <!-- Already registered (inline link) -->
    <div class="text-center">
        <p class="text-sm text-gray-600">Already registered? <a href="{{ route('login') }}" class="font-semibold text-sky-600 no-underline transition-all hover:underline hover:text-sky-500 ml-1.5">Sign in</a></p>
    </div>

    <!-- Vanilla JS for Password Toggle -->
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const button = document.querySelector('.toggle-password');
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            button.textContent = isPassword ? 'Hide' : 'Show';
        }
    </script>
</x-guest-layout>
