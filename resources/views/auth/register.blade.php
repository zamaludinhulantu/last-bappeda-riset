<x-guest-layout>
    <div class="space-y-3 text-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ __('Daftar Akun Peneliti') }}</h1>
        <p class="text-sm text-gray-500">Aktifkan akses untuk mengunggah penelitian.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="nama" :value="__('Nama')" />
            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="surel" :value="__('Email')" />
            <x-text-input id="surel" class="block mt-1 w-full" type="email" name="surel" :value="old('surel')" required autocomplete="username" />
            <p class="mt-1 text-[11px] text-gray-500">Pakailah email yang aktif supaya verifikasi dan notifikasi bisa masuk.</p>
            <x-input-error :messages="$errors->get('surel')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="kata_sandi" :value="__('Kata Sandi')" />

            <x-text-input id="kata_sandi" class="block mt-1 w-full"
                            type="password"
                            name="kata_sandi"
                            required autocomplete="new-password" />

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

        <div class="flex items-center justify-between pt-2">
            <a class="text-sm text-orange-600 hover:text-orange-700 font-semibold" href="{{ route('login') }}">
                {{ __('Sudah punya akun? Masuk') }}
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800">
                <i class="fas fa-user-plus text-xs"></i>
                {{ __('Daftar') }}
            </button>
        </div>
    </form>
</x-guest-layout>
