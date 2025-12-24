<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Guru BK') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500 uppercase font-bold">Total Siswa</p>
                    <p class="text-2xl font-black">{{ $stats['total_siswa'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500 uppercase font-bold">Pending Konsultasi</p>
                    <p class="text-2xl font-black text-yellow-600">{{ $stats['pending_konsultasi'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                    <p class="text-sm text-gray-500 uppercase font-bold">Total Pembinaan</p>
                    <p class="text-2xl font-black text-green-600">{{ $stats['total_pembinaan'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-500">
                    <p class="text-sm text-gray-500 uppercase font-bold">Izin Hari Ini</p>
                    <p class="text-2xl font-black text-red-600">{{ $stats['izin_hari_ini'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Konsultasi -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Permintaan Konsultasi Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Perihal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($recent_konsultasi as $rk)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $rk->siswa->nama_lengkap }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $rk->perihal }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold uppercase 
                                            {{ $rk->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100' }}">
                                            {{ $rk->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Percakapan Chat -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Percakapan Terbaru</h3>
                        <a href="{{ route('bk.chat.index') }}" class="text-xs font-bold text-red-600 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($chat_rooms as $room)
                        <a href="{{ route('bk.chat.index', ['room_id' => $room->id]) }}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                            <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center text-white font-black text-sm relative">
                                {{ substr($room->siswa->name, 0, 1) }}
                                @if($room->unread_count > 0)
                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-600 border-2 border-white rounded-full flex items-center justify-center text-[8px] font-bold">
                                    {{ $room->unread_count }}
                                </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline">
                                    <h4 class="text-sm font-bold text-gray-900 truncate">{{ $room->siswa->name }}</h4>
                                    <span class="text-[10px] text-gray-400 font-medium">
                                        {{ $room->last_message_at ? \Carbon\Carbon::parse($room->last_message_at)->diffForHumans() : '' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 truncate mt-0.5">
                                    {{ $room->messages->first()?->message ?? ($room->messages->first()?->type != 'text' ? '[Lampiran]' : 'Belum ada pesan') }}
                                </p>
                            </div>
                        </a>
                        @empty
                        <div class="py-10 text-center">
                            <p class="text-sm text-gray-400 font-medium">Belum ada percakapan</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
