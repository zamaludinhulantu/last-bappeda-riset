@extends('layouts.public')

@section('title', 'Kontak | '.config('app.name','Aplikasi'))

@section('hero')
    <div class="max-w-3xl">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Hubungi Kami</p>
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Tim Pengelola Portal Penelitian</h1>
        <p class="text-gray-600 mt-4 text-lg">{{ $contactInfo->value('subjudul') }}</p>
    </div>
@endsection

@section('content')
    @php
        $email = $contactInfo->value('surel');
        $phone = $contactInfo->value('telepon');
        $address = $contactInfo->value('alamat');
        $serviceHours = $contactInfo->value('jam_layanan');
    @endphp
    <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
        <div class="grid gap-6 sm:grid-cols-2 text-sm text-gray-600">
            @if($email)
                <div>
                    <p class="text-xs uppercase font-semibold text-gray-500">Email</p>
                    <p class="text-gray-900 mt-1">{{ $email }}</p>
                </div>
            @endif
            @if($phone)
                <div>
                    <p class="text-xs uppercase font-semibold text-gray-500">Telepon</p>
                    <p class="text-gray-900 mt-1">{{ $phone }}</p>
                </div>
            @endif
            @if($address)
                <div>
                    <p class="text-xs uppercase font-semibold text-gray-500">Alamat</p>
                    <p class="text-gray-900 mt-1">{{ $address }}</p>
                </div>
            @endif
            @if($serviceHours)
                <div>
                    <p class="text-xs uppercase font-semibold text-gray-500">Jam Layanan</p>
                    <p class="text-gray-900 mt-1">{{ $serviceHours }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection
