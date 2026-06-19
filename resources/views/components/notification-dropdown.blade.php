@if (Auth::check())
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="relative text-gray-500 hover:text-gray-700 focus:outline-none">
            <i class="fa-solid fa-bell fa-lg"></i>
            @if ($unreadCount > 0)
                <span
                    class="absolute top-0 right-0 block h-2 w-2 transform -translate-y-1/2 translate-x-1/2 rounded-full bg-red-500 ring-2 ring-white"></span>
            @endif
        </button>

        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-20"
            style="display: none;">

            <div class="py-2 px-4 border-b flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Notifikasi</h3>
                <span class="text-xs text-white bg-red-500 rounded-full px-2 py-0.5">{{ $unreadCount }}</span>
            </div>

            <div class="divide-y max-h-80 overflow-y-auto">
                @php
                    // Tentukan link tujuan berdasarkan peran user
                    $linkTujuan = Auth::user()->hasRole('Siswa')
                        ? route('izin.index')
                        : route('wali-kelas.perizinan.index');
                @endphp
                @forelse($notifications as $notification)
                    <a href="{{ route('shared.notifications.read', $notification->id) }}" class="flex items-start px-4 py-3 hover:bg-gray-100">
                        <div class="flex-shrink-0 pt-1">
                            @if ($notification->type == 'App\Notifications\PengajuanIzinMasuk')
                                <i class="fa-solid fa-file-import text-blue-500"></i>
                            @elseif($notification->type == 'App\Notifications\IzinDisetujuiNotification')
                                <i class="fa-solid fa-check-circle text-green-500"></i>
                            @else
                                <i class="fa-solid fa-times-circle text-red-500"></i>
                            @endif
                        </div>
                        <div class="text-gray-600 text-sm mx-2">
                            <p class="font-bold">{{ $notification->data['title'] }}</p>
                            <p>{{ $notification->data['message'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-4 text-sm text-gray-500">
                        Tidak ada notifikasi baru.
                    </div>
                @endforelse
            </div>

            @if ($unreadCount > 0)
                <a href="{{ $linkTujuan }}"
                    class="block bg-gray-100 text-gray-700 text-center font-semibold py-2 hover:bg-gray-200">Lihat
                    Semua</a>
            @endif
        </div>
    </div>
@endif
