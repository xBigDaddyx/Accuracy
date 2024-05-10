<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="teresa">

<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="{{ asset('storage/images/favicon.ico') }}">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Nunito:400,700&display=swap');
    </style>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <style>
        @media(prefers-color-scheme: dark) {
            .bg-dots {
                background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(200,200,255,0.15)'/%3E%3C/svg%3E");
            }
        }

        @media(prefers-color-scheme: light) {
            .bg-dots {
                background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,50,0.10)'/%3E%3C/svg%3E")
            }
        }
    </style>
    @vite('resources/css/app.css')
    <wireui:scripts />
    @livewireStyles



    @livewire('notifications')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <x-livewire-alert::scripts />
    @vite('resources/js/app.js')
    @stack('scripts')


</head>

<body class="min-h-screen font-sans antialiased bg-dots bg-base-300 ">

    @include('accuracy.layouts.navbar')
    <x-mary-main full-width>

        {{-- This is a sidebar that works also as a drawer on small screens --}}
        {{-- Notice the `main-drawer` reference here --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100">

            {{-- User --}}
            @if($user = auth()->user())
            <x-mary-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="pt-2">
                <x-slot:actions>
                    <x-mary-button icon="o-power" class="btn-circle btn-error  btn-xs" tooltip-left="logoff" no-wire-navigate link="/logout" />
                </x-slot:actions>
            </x-mary-list-item>

            <x-mary-menu-separator />
            @endif

            {{-- Activates the menu item when a route matches the `link` property --}}
            <x-mary-menu active-bg-color="bg-primary" activate-by-route>
                <x-mary-menu-item title="Home" icon="s-home" link="/" />
                <x-mary-menu-item title="Carton Check" icon="s-document-magnifying-glass" link="accuracy/carton/check" />

            </x-mary-menu>
        </x-slot:sidebar>


        <x-slot:content>
            @yield('content')
        </x-slot:content>
    </x-mary-main>
    <x-mary-toast />


    @livewireScripts

</body>

</html>
