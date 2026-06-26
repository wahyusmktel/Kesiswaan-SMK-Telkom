<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Data Penempatan Prakerin') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4"><a
                            href="{{ route('prakerin.penempatan.create') }}"><x-primary-button>{{ __('+ Tempatkan Siswa') }}</x-primary-button></a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Industri
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($penempatan as $item)
                                    <tr>
                                        <td class="px-6 py-4">{{ $item->siswa?->nama_lengkap ?? 'Data siswa tidak ditemukan' }}</td>
                                        <td class="px-6 py-4">
                                            @if($item->industri)
                                                {{ $item->industri->nama_industri }}
                                            @else
                                                <span class="text-red-600 font-semibold">Data industri tidak ditemukan</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->isoFormat('D MMM YY') }} -
                                            {{ \Carbon\Carbon::parse($item->tanggal_selesai)->isoFormat('D MMM YY') }}
                                        </td>
                                        <td class="px-6 py-4"><span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $item->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $penempatan->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
