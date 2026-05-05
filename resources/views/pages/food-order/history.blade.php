<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">

            <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-2xl font-black text-gray-900 mb-1">Pesanan Saya</h3>
                <p class="text-gray-500 font-medium text-sm">Lacak status pesanan kantin kamu di sini.</p>
            </div>

            <div class="space-y-6">
                @forelse($orders as $order)
                <div class="bg-white rounded-[24px] shadow-md border border-gray-100 overflow-hidden relative group">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-black">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 leading-tight">{{ $order->kantin->kantinProfile->name ?? $order->kantin->name }}</h4>
                                <p class="text-xs font-bold text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wide
                                {{ $order->status == 'pending' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $order->status == 'preparing' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $order->status == 'ready' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $order->status == 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $order->status == 'cancelled' ? 'bg-gray-100 text-gray-700' : '' }}">
                                
                                @if($order->status == 'pending') <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span> Menunggu Konfirmasi
                                @elseif($order->status == 'preparing') <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Sedang Dimasak
                                @elseif($order->status == 'ready') <span class="w-2 h-2 rounded-full bg-blue-500"></span> Siap Diambil
                                @elseif($order->status == 'completed') <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai
                                @else <span class="w-2 h-2 rounded-full bg-gray-500"></span> Dibatalkan @endif
                            </span>
                            <div class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-widest">ID: {{ $order->order_number }}</div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @foreach($order->items as $item)
                        <div class="flex justify-between items-center mb-3 last:mb-0">
                            <div class="font-bold text-gray-700 text-sm flex items-center gap-2">
                                <span class="text-gray-500">{{ $item->quantity }}x</span>
                                {{ $item->menu_name }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="bg-gray-50 p-4 px-6 border-t border-gray-100 flex justify-between items-center">
                        <div class="text-xs font-bold text-gray-500 uppercase">Total Belanja</div>
                        <div class="text-lg font-black text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                    </div>
                </div>
                @empty
                <div class="py-16 text-center bg-white rounded-[24px] shadow-sm border border-gray-100">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 text-gray-400 rounded-full mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-2">Belum Ada Pesanan</h4>
                    <p class="text-gray-500 mb-6 font-medium">Kamu belum pernah memesan makanan apapun.</p>
                    <a href="{{ route('food-order.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-xl transition-colors">Pesan Sekarang</a>
                </div>
                @endforelse
                
                @if($orders->hasPages())
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
