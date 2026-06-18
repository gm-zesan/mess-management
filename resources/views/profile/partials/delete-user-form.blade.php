<section class="space-y-3" x-data="userDeletion()">
    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-xs text-red-800">
            {{ __('Warning: Deleting your account is permanent. All data will be lost.') }}
        </p>
    </div>

    <button 
        type="button"
        @click="checkEligibility()"
        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
    >
        {{ __('Delete Account') }}
    </button>

    <!-- Manager Transfer Modal -->
    <x-modal name="transfer-manager-role" :show="false" focusable>
        <div class="p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 6v2m9-6a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">{{ __('Transfer Manager Role') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Required before account deletion') }}</p>
                </div>
            </div>

            <p class="text-xs text-gray-700 mb-4">
                {{ __('You are the manager of one or more messes. Please transfer the manager role to another member before you can delete your account.') }}
            </p>

            <!-- List of messes where user is manager -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs font-semibold text-gray-700 mb-2">{{ __('Messes requiring transfer:') }}</p>
                <ul id="managerMesses" class="space-y-1">
                    <!-- Populated by JavaScript -->
                </ul>
            </div>

            <!-- Transfer form for each mess -->
            <div id="transferForms" class="space-y-4">
                <!-- Populated by JavaScript -->
            </div>

            <div class="flex gap-2 pt-3 border-t border-gray-200">
                <button 
                    type="button"
                    @click="$dispatch('close')"
                    class="flex-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-900 text-xs font-semibold rounded-lg transition-colors"
                >
                    {{ __('Cancel') }}
                </button>
                <button 
                    type="button"
                    @click="proceedToDelete()"
                    id="proceedDeleteBtn"
                    class="flex-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled
                >
                    {{ __('Proceed to Delete') }}
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Standard Delete Confirmation Modal -->
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

    <script>
        function userDeletion() {
            return {
                async checkEligibility() {
                    try {
                        const response = await fetch('{{ route("profile.check-deletion") }}', {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            }
                        });
                        const data = await response.json();

                        if (data.allowed) {
                            // User can delete directly
                            this.$dispatch('open-modal', 'confirm-user-deletion');
                        } else if (data.reason === 'manager') {
                            // Show manager transfer modal
                            this.showManagerTransferModal(data);
                        }
                    } catch (error) {
                        console.error('Error checking deletion eligibility:', error);
                        alert('An error occurred. Please try again.');
                    }
                },

                showManagerTransferModal(data) {
                    // Store data for later use
                    window.deletionData = data;

                    // Populate messes list
                    const messesList = document.getElementById('managerMesses');
                    messesList.innerHTML = data.messes.map(mess => 
                        `<li class="text-xs text-gray-700">• <strong>${mess.name}</strong></li>`
                    ).join('');

                    // Populate transfer forms
                    const formsContainer = document.getElementById('transferForms');
                    formsContainer.innerHTML = data.messes.map(mess => `
                        <div class="p-3 bg-gray-50 rounded border border-gray-200">
                            <p class="text-xs font-semibold text-gray-700 mb-2">Transfer manager for <strong>${mess.name}</strong> to:</p>
                            <select data-mess-id="${mess.id}" class="mess-transfer-select w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                                <option value="">-- Select new manager --</option>
                                ${data.candidates.map(user => 
                                    `<option value="${user.id}">${user.name} (${user.email})</option>`
                                ).join('')}
                            </select>
                        </div>
                    `).join('');

                    // Add change listeners
                    document.querySelectorAll('.mess-transfer-select').forEach(select => {
                        select.addEventListener('change', () => this.checkAllTransfersSelected(data.messes));
                    });

                    // Initially disable button
                    document.getElementById('proceedDeleteBtn').disabled = true;

                    this.$dispatch('open-modal', 'transfer-manager-role');
                },

                checkAllTransfersSelected(messes) {
                    const allSelected = messes.every(mess => {
                        const select = document.querySelector(`[data-mess-id="${mess.id}"]`);
                        return select && select.value !== '';
                    });

                    document.getElementById('proceedDeleteBtn').disabled = !allSelected;
                },

                async proceedToDelete() {
                    const transfers = [];
                    document.querySelectorAll('.mess-transfer-select').forEach(select => {
                        if (select.value) {
                            transfers.push({
                                mess_id: select.dataset.messId,
                                new_manager_id: select.value
                            });
                        }
                    });

                    // Send transfers to backend
                    try {
                        const response = await fetch('{{ route("profile.transfer-manager") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ transfers: transfers })
                        });

                        const result = await response.json();
                        
                        if (result.success) {
                            // Close modal and show delete confirmation
                            this.$dispatch('close');
                            this.$dispatch('open-modal', 'confirm-user-deletion');
                        } else {
                            alert('Error transferring manager roles: ' + (result.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error transferring manager roles:', error);
                        alert('An error occurred while transferring manager roles. Please try again.');
                    }
                }
            }
        }
    </script>
</section>
