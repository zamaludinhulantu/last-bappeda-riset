<x-guest-layout>
    <div class="space-y-2 mb-6 text-center">
        <h1 class="text-2xl font-semibold text-gray-900">{{ __('Masuk ke SIPRISDA') }}</h1>
        <p class="text-sm text-gray-500">Login untuk lanjut ke dashboard.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="surel" :value="__('Email')" />
            <x-text-input id="surel" class="block mt-1 w-full" type="email" name="surel" :value="old('surel')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('surel')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="kata_sandi" :value="__('Kata Sandi')" />
                <button type="button"
                        class="text-xs font-semibold text-gray-500 hover:text-gray-700"
                        data-toggle-password="kata_sandi"
                        aria-label="Tampilkan kata sandi"
                        aria-pressed="false">
                    <span data-show-text>Tampilkan</span>
                    <span data-hide-text class="hidden">Sembunyikan</span>
                </button>
            </div>

            <x-text-input id="kata_sandi" class="block mt-1 w-full"
                            type="password"
                            name="kata_sandi"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('kata_sandi')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-orange-600 hover:text-orange-700 font-semibold" href="{{ route('password.request') }}">
                    {{ __('Lupa sandi?') }}
                </a>
            @endif
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-800">
                <i class="fas fa-sign-in-alt text-xs"></i>
                {{ __('Masuk') }}
            </button>
            @if (Route::has('register'))
                <p class="text-sm text-gray-600 mt-3 text-center">
                    {{ __('Belum punya akun?') }}
                    <a href="{{ route('register') }}" class="text-orange-600 hover:text-orange-700 font-semibold">
                        {{ __('Daftar') }}
                    </a>
                </p>
            @endif
            <p class="text-xs text-gray-500 mt-3">Kembali ke <a href="{{ url('/') }}" class="text-orange-600 font-semibold">beranda publik</a>.</p>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggles = document.querySelectorAll('[data-toggle-password]');
            toggles.forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const inputId = toggle.getAttribute('data-toggle-password');
                    const input = document.getElementById(inputId);
                    if (!input) return;

                    const willShow = input.type === 'password';
                    input.type = willShow ? 'text' : 'password';
                    toggle.setAttribute('aria-pressed', willShow ? 'true' : 'false');

                    const showText = toggle.querySelector('[data-show-text]');
                    const hideText = toggle.querySelector('[data-hide-text]');
                    if (showText && hideText) {
                        showText.classList.toggle('hidden', willShow);
                        hideText.classList.toggle('hidden', !willShow);
                    }
                });
            });
        });
    </script>
</x-guest-layout>
