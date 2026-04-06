<form method="post" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    @method('put')

    <!-- Current Password Field -->
    <div>
        <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-xs font-semibold text-gray-900" />
        <x-text-input 
            id="update_password_current_password" 
            name="current_password" 
            type="password" 
            class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all"
            autocomplete="current-password"
            placeholder="Enter your current password"
        />
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1 text-xs text-red-600" />
    </div>

    <!-- New Password Field -->
    <div>
        <x-input-label for="update_password_password" :value="__('New Password')" class="text-xs font-semibold text-gray-900" />
        <x-text-input 
            id="update_password_password" 
            name="password" 
            type="password" 
            class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all"
            autocomplete="new-password"
            placeholder="Enter a strong new password"
        />
        <p class="mt-0.5 text-xs text-gray-500">At least 8 characters with uppercase, lowercase, numbers</p>
        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1 text-xs text-red-600" />
    </div>

    <!-- Confirm Password Field -->
    <div>
        <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-xs font-semibold text-gray-900" />
        <x-text-input 
            id="update_password_password_confirmation" 
            name="password_confirmation" 
            type="password" 
            class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all"
            autocomplete="new-password"
            placeholder="Confirm your new password"
        />
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1 text-xs text-red-600" />
    </div>

    <!-- Submit Button -->
    <div class="flex items-center gap-3 pt-3 border-t border-gray-200">
        <button 
            type="submit" 
            class="px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
        >
            {{ __('Update Password') }}
        </button>

        @if (session('status') === 'password-updated')
            <p class="text-xs text-green-600 font-medium">{{ __('Password updated successfully.') }}</p>
        @endif
    </div>
</form>
