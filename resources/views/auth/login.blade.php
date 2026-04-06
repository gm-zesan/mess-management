<x-guest-layout>
    <!-- Heading -->
    <div class="text-center mb-12">
        <h1 class="font-[family-name:var(--font-sora)] text-2xl font-bold text-gray-900 mb-1">Welcome back</h1>
        <p class="text-sm font-light text-gray-600">Sign in to access your account</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
        @csrf

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
                    autofocus
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
                    autocomplete="current-password"
                    class="w-full rounded-md border border-gray-300 bg-white py-2.5 pl-10 pr-16 text-sm font-normal text-gray-900 placeholder-gray-400 transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 {{ $errors->has('password') ? 'border-red-500' : '' }}"
                    placeholder="••••••••"
                />
                <button type="button" class="absolute right-3.5 bg-none border-none cursor-pointer px-2 py-1 text-xs font-semibold text-sky-600 transition-colors hover:text-sky-500 active:text-sky-700" onclick="togglePassword()" data-toggle="password">
                    <span class="toggle-text">Show</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="text-xs font-medium text-red-500 mt-1" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between text-xs">
            <label class="flex cursor-pointer items-center gap-2 text-gray-600 font-medium transition-colors hover:text-gray-900">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 cursor-pointer rounded accent-sky-600 border"
                />
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="font-semibold text-sky-600 no-underline transition-all hover:underline hover:text-sky-500">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Sign In Button -->
        <button type="submit" class="w-full rounded-md bg-sky-600 px-4 py-2.5 font-[family-name:var(--font-sora)] text-sm font-semibold uppercase tracking-wider text-white transition-all hover:bg-sky-700 active:scale-98 focus:outline-none focus:ring-2 focus:ring-sky-100 mt-1">
            Sign In
        </button>

        <!-- Divider -->
        <div class="relative my-0 text-center">
            <div class="absolute inset-y-1/2 left-0 right-0 h-px bg-gray-300 -translate-y-1/2"></div>
            <span class="relative inline-block bg-slate-50 px-3 text-xs font-medium uppercase tracking-wider text-gray-600">or</span>
        </div>

        <!-- Google Login Button -->
        <a href="{{ route('auth.google.redirect') }}" class="inline-flex w-full items-center justify-center gap-2.5 rounded-md border border-gray-300 bg-white px-4 py-2.5 font-[family-name:var(--font-sans)] text-sm font-semibold text-gray-900 transition-all hover:bg-gray-50 active:scale-98 focus:outline-none focus:ring-2 focus:ring-sky-100 mb-4">
            <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 flex-shrink-0">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            <span>Continue with Google</span>
        </a>
    </form>

    <!-- Create Account (inline link) -->
    <div class="text-center">
        <p class="text-sm text-gray-600 mb-3">Don't have an account? <a href="{{ route('register') }}" class="font-semibold text-sky-600 no-underline transition-all hover:underline hover:text-sky-500 ml-1.5">Create Account</a></p>
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
