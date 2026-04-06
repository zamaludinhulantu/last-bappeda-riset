<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Lupa kata sandi? Masukkan email Anda, kami akan kirim tautan reset ke email terdaftar.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="surel" :value="__('Surel')" />
            <x-text-input id="surel" class="block mt-1 w-full" type="email" name="surel" :value="old('surel')" required autofocus />
            <x-input-error :messages="$errors->get('surel')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Kirim Tautan Reset Kata Sandi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
