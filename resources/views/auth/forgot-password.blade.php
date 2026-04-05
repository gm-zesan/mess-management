<x-guest-layout>
    <!-- Heading -->
    <div class="text-center mb-12">
        <h1 class="font-[family-name:var(--font-sora)] text-2xl font-bold text-gray-900 mb-1">Reset your password</h1>
        <p class="text-sm font-light text-gray-600">Enter your email to receive a password reset link</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-6 flex gap-3 rounded-md border border-red-200 bg-red-50 p-4">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <div class="flex-1">
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium text-red-700">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Password Reset Form -->
    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
        @csrf

        <!-- Email Address -->
        <div class="flex flex-col gap-1">
            <label for="email" class="text-xs font-semibold uppercase tracking-wider text-gray-900">Email Address</label>
            <div class="relative flex items-center">
                <svg class="absolute left-3.5 z-10 h-5 w-5 text-gray-500 pointer-events-none" fill="currentColor" viewBox="0 0 20 20">
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
                    autocomplete="email"
                    class="w-full rounded-md border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm font-normal text-gray-900 placeholder-gray-400 transition-all focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100 {{ $errors->has('email') ? 'border-red-500' : '' }}"
                    placeholder="you@company.com"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="text-xs font-medium text-red-500 mt-1" />
        </div>

        <!-- Send Reset Link Button -->
        <button type="submit" class="w-full rounded-md bg-sky-600 px-4 py-2.5 font-[family-name:var(--font-sora)] text-sm font-semibold uppercase tracking-wider text-white transition-all hover:bg-sky-700 active:scale-98 focus:outline-none focus:ring-2 focus:ring-sky-100 mt-1 mb-4">
            Send Reset Link
        </button>
    </form>

    <!-- Remember your password (inline link) -->
    <div class="text-center">
        <p class="text-sm text-gray-600">Remember your password? <a href="{{ route('login') }}" class="font-semibold text-sky-600 no-underline transition-all hover:underline hover:text-sky-500 ml-1.5">Sign in</a></p>
    </div>
</x-guest-layout>
