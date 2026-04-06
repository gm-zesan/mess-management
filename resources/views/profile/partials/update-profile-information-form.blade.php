<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf
    @method('patch')

    <!-- Name Field -->
    <div>
        <x-input-label for="name" :value="__('Full Name')" class="text-xs font-semibold text-gray-900" />
        <x-text-input 
            id="name" 
            name="name" 
            type="text" 
            class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all" 
            :value="old('name', $user->name)" 
            required 
            autofocus 
            autocomplete="name" 
            placeholder="Enter your full name"
        />
        <x-input-error class="mt-1 text-xs text-red-600" :messages="$errors->get('name')" />
    </div>

    <!-- Email Field -->
    <div>
        <x-input-label for="email" :value="__('Email Address')" class="text-xs font-semibold text-gray-900" />
        <x-text-input 
            id="email" 
            name="email" 
            type="email" 
            class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all" 
            :value="old('email', $user->email)" 
            required 
            autocomplete="username"
            placeholder="Enter your email address"
        />
        <x-input-error class="mt-1 text-xs text-red-600" :messages="$errors->get('email')" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-xs text-yellow-800">
                    {{ __('Your email address is unverified.') }}
                </p>
                <button 
                    form="send-verification" 
                    class="mt-1 text-xs font-semibold text-yellow-600 hover:text-yellow-700 transition-colors"
                >
                    {{ __('Click here to re-send the verification email.') }}
                </button>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-xs font-semibold text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            </div>
        @endif
    </div>

    <!-- Submit Button -->
    <div class="flex items-center gap-3 pt-3 border-t border-gray-200">
        <button 
            type="submit" 
            class="px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
        >
            {{ __('Save Changes') }}
        </button>

        @if (session('status') === 'profile-updated')
            <p class="text-xs text-green-600 font-medium">{{ __('Saved successfully.') }}</p>
        @endif
    </div>
</form>
