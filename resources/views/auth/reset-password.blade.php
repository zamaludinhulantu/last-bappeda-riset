<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="surel" :value="__('Surel')" />
            <x-text-input id="surel" class="block mt-1 w-full" type="email" name="surel" :value="old('surel', $request->surel)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('surel')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="kata_sandi" :value="__('Kata Sandi')" />
            <x-text-input id="kata_sandi" class="block mt-1 w-full" type="password" name="kata_sandi" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('kata_sandi')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="kata_sandi_confirmation" :value="__('Konfirmasi Kata Sandi')" />

            <x-text-input id="kata_sandi_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="kata_sandi_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('kata_sandi_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Kata Sandi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
