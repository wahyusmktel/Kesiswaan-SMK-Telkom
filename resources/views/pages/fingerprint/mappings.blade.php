<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Mapping Pegawai Fingerprint</h2>
            <p class="text-sm text-gray-500 mt-0.5">Hubungkan user ID mesin fingerprint ke pegawai sistem secara manual.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('pages.fingerprint.partials.flash')
        @include('pages.fingerprint.partials.user-table', [
            'fingerprintUsers' => $fingerprintUsers,
            'allDevices' => $allDevices,
            'employees' => $employees,
            'title' => 'Mapping User Mesin ke Pegawai',
            'editable' => true,
            'action' => route('fingerprint.mappings'),
        ])
    </div>
</x-app-layout>
