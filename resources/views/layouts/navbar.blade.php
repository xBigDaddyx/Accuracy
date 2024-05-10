<x-mary-nav sticky class="lg:hidden bg-base-100">
    <x-slot:brand>
        <div class="ml-5 pt-5"><img class="w-10 h-10" src="{{ asset('storage/images/teresa-logo-f.png') }}"></div>
    </x-slot:brand>
    <x-slot:actions>
        <label for="main-drawer" class="lg:hidden mr-3">
            <x-mary-icon name="o-bars-3" class="cursor-pointer" />
        </label>
    </x-slot:actions>
</x-mary-nav>
