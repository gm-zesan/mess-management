<section class="space-y-3">
    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-xs text-red-800">
            {{ __('Warning: Deleting your account is permanent. All data will be lost.') }}
        </p>
    </div>

    <button 
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
    >
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 6v2m9-6a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('Delete Account') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('This action cannot be undone') }}</p>
                </div>
            </div>

            <p class="text-xs text-gray-700 mb-3">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-3">
                @csrf
                @method('delete')

                <div>
                    <x-input-label for="password" value="{{ __('Password') }}" class="text-xs font-semibold text-gray-900" />
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                        placeholder="{{ __('Enter your password to confirm') }}"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1 text-xs text-red-600" />
                </div>

                <div class="flex gap-2 pt-3 border-t border-gray-200">
                    <button 
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="flex-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-900 text-xs font-semibold rounded-lg transition-colors"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>
