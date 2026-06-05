<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Log Absensi Fingerprint</h2>
            <p class="text-sm text-gray-500 mt-0.5">Riwayat hasil tarik log dari mesin GF1600 / ZKTeco</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('pages.fingerprint.partials.flash')
        @include('pages.fingerprint.partials.log-table', ['attendances' => $attendances, 'allDevices' => $allDevices, 'compact' => false])
    </div>
</x-app-layout>
