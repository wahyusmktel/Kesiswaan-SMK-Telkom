<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] text-red-600 uppercase tracking-widest font-extrabold mb-1">Pusat Bantuan</p>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                    Notifikasi Saya
                </h2>
            </div>
            @if($notifications->where('read_at', null)->count() > 0)
                <form action="{{ route('shared.notifications.mark-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 text-red-700 text-sm font-bold hover:bg-red-100 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="divide-y divide-gray-50">
                @forelse($notifications as $notification)
                    <div class="group relative flex items-start gap-4 p-6 transition-all {{ $notification->unread() ? 'bg-red-50/30' : 'hover:bg-gray-50' }}">
                        {{-- Status Indicator --}}
                        @if($notification->unread())
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-600 rounded-r-full"></div>
                        @endif

                        {{-- Icon --}}
                        <div class="w-12 h-12 rounded-2xl {{ $notification->unread() ? 'bg-red-600 text-white shadow-lg shadow-red-100' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4 mb-1">
                                <p class="text-sm font-bold {{ $notification->unread() ? 'text-gray-900' : 'text-gray-600' }}">
                                    {{ $notification->data['message'] }}
                                </p>
                                <span class="text-[10px] font-medium text-gray-400 whitespace-nowrap bg-gray-50 px-2 py-1 rounded-full uppercase tracking-wider">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-4 mt-3">
                                <a href="{{ route('shared.notifications.read', $notification->id) }}" 
                                   class="inline-flex items-center gap-1.5 text-xs font-black text-red-600 uppercase tracking-widest hover:text-red-700 transition-colors">
                                    Lihat Detail
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                                
                                @if($notification->unread())
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span class="text-[10px] font-black text-red-600 uppercase tracking-widest">Belum Dibaca</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Notifikasi</h3>
                        <p class="text-sm text-gray-500 max-w-xs mx-auto">Kami akan memberitahu Anda di sini setiap kali ada aktivitas baru pada perizinan Anda.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
