<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Layanan Orderan Kantin') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full" x-data="orderManager()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            @endif

            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-[24px] shadow-xl border border-gray-100 gap-4">
                <div>
                    <h3 class="text-2xl font-black text-gray-900">Manajemen Pesanan Masuk</h3>
                    <p class="text-gray-500 font-medium">Lihat dan kelola pesanan siswa secara real-time.</p>
                </div>
                
                <div class="flex gap-2 bg-gray-100 p-1.5 rounded-xl overflow-x-auto w-full md:w-auto">
                    <a href="{{ route('kantin.orders.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ !request('status') || request('status') == 'aktif' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200' }}">Pesanan Aktif</a>
                    <a href="{{ route('kantin.orders.index', ['status' => 'pending']) }}" class="px-5 py-2.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ request('status') == 'pending' ? 'bg-white shadow-sm text-red-600' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200' }}">Pesanan Baru</a>
                    <a href="{{ route('kantin.orders.index', ['status' => 'completed']) }}" class="px-5 py-2.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ request('status') == 'completed' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200' }}">Selesai</a>
                    <a href="{{ route('kantin.orders.index', ['status' => 'all']) }}" class="px-5 py-2.5 rounded-lg text-sm font-bold whitespace-nowrap transition-colors {{ request('status') == 'all' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200' }}">Semua Order</a>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                @forelse($orders as $order)
                <div class="bg-white rounded-[24px] shadow-lg border border-gray-100 p-6 flex flex-col hover:shadow-xl transition-shadow relative overflow-hidden group">
                    {{-- Status Badge Background --}}
                    <div class="absolute top-0 right-0 w-32 h-32 rounded-bl-full -mr-8 -mt-8 opacity-10 pointer-events-none
                        {{ $order->status == 'pending' ? 'bg-red-500' : '' }}
                        {{ $order->status == 'preparing' ? 'bg-amber-500' : '' }}
                        {{ $order->status == 'ready' ? 'bg-blue-500' : '' }}
                        {{ $order->status == 'completed' ? 'bg-emerald-500' : '' }}
                        {{ $order->status == 'cancelled' ? 'bg-gray-500' : '' }}">
                    </div>

                    <div class="flex justify-between items-start mb-6 relative z-10 border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center font-black text-xl text-gray-400">
                                {{ substr($order->student->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-900 leading-tight">{{ $order->student->name }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">ID: {{ $order->order_number }}</span>
                                    <span class="text-xs font-bold text-gray-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $order->created_at->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-orange-500 mb-1">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wide
                                {{ $order->status == 'pending' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $order->status == 'preparing' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $order->status == 'ready' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $order->status == 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $order->status == 'cancelled' ? 'bg-gray-100 text-gray-700' : '' }}">
                                
                                @if($order->status == 'pending')
                                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span> Pesanan Baru
                                @elseif($order->status == 'preparing')
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Dimasak
                                @elseif($order->status == 'ready')
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Siap Diambil
                                @elseif($order->status == 'completed')
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai
                                @else
                                    <span class="w-2 h-2 rounded-full bg-gray-500"></span> Dibatalkan
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1 space-y-3 mb-6 relative z-10">
                        @foreach($order->items as $item)
                        <div class="flex justify-between items-center text-sm">
                            <div class="font-bold text-gray-700 flex items-center gap-2">
                                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md font-black">{{ $item->quantity }}x</span>
                                {{ $item->menu_name }}
                            </div>
                            <div class="font-bold text-gray-500">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                        @endforeach
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <div class="text-sm font-bold text-gray-500">Pembayaran:</div>
                            <div class="text-sm font-black uppercase {{ $order->payment_method == 'qris' ? 'text-blue-600' : 'text-emerald-600' }}">
                                {{ $order->payment_method }}
                            </div>
                        </div>
                        
                        @if($order->notes)
                        <div class="bg-amber-50 text-amber-800 p-3 rounded-xl text-sm font-medium border border-amber-200 mt-4">
                            <strong class="font-black block mb-1">Catatan Siswa:</strong>
                            "{{ $order->notes }}"
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex gap-3 mt-auto relative z-10 border-t border-gray-100 pt-6">
                        @if($order->status == 'pending')
                            <form action="{{ route('kantin.orders.update-status', $order->id) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="preparing">
                                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-3 rounded-xl shadow-lg shadow-orange-500/30 transition-all flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>
                                    Mulai Masak
                                </button>
                            </form>
                        @elseif($order->status == 'preparing')
                            <form action="{{ route('kantin.orders.update-status', $order->id) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="ready">
                                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-black py-3 rounded-xl shadow-lg shadow-blue-500/30 transition-all flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Makanan Siap
                                </button>
                            </form>
                        @elseif($order->status == 'ready')
                            <form action="{{ route('kantin.orders.update-status', $order->id) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-black py-3 rounded-xl shadow-lg shadow-emerald-500/30 transition-all flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Pesanan Selesai (Diambil)
                                </button>
                            </form>
                        @endif

                        <button onclick="openReceiptWindow('{{ route('kantin.orders.receipt', $order->id) }}')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-6 rounded-xl transition-colors flex justify-center items-center gap-2 flex-shrink-0" title="Cetak Struk">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Struk
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center bg-white rounded-[24px] shadow-xl border border-gray-100">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-50 text-gray-400 mb-6 relative">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span class="absolute top-0 right-0 w-6 h-6 bg-red-500 rounded-full border-4 border-white animate-pulse"></span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Belum Ada Pesanan</h3>
                    <p class="text-gray-500 font-medium max-w-sm mx-auto">Saat ini belum ada pesanan yang sesuai dengan filter. Sistem akan otomatis memantau pesanan masuk secara real-time.</p>
                </div>
                @endforelse
            </div>
            
            @if($orders->hasPages())
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>
    </div>

    <script>
        function openReceiptWindow(url) {
            window.open(url, 'ReceiptWindow', 'width=400,height=600');
        }

        function orderManager() {
            return {
                lastCount: {{ \App\Models\KantinOrder::where('kantin_id', Auth::id())->where('status', 'pending')->count() }},
                audio: new Audio('/sounds/notification.mp3'), // Assuming a sound exists
                
                init() {
                    setInterval(() => {
                        this.checkNewOrders();
                    }, 5000); // Check every 5 seconds
                },

                async checkNewOrders() {
                    try {
                        const response = await fetch('{{ route('kantin.api.orders.pending-count') }}');
                        const data = await response.json();
                        
                        if (data.count > this.lastCount) {
                            // New order arrived
                            // Reload the page to show new orders if we are on the active/pending tab
                            if (!window.location.search.includes('status=completed') && !window.location.search.includes('status=all')) {
                                window.location.reload();
                            }
                        }
                        this.lastCount = data.count;
                        
                        // Emit event for global top bar / dashboard badge
                        window.dispatchEvent(new CustomEvent('kantin-orders-updated', { detail: { count: data.count } }));
                        
                    } catch (error) {
                        console.error('Error fetching orders:', error);
                    }
                }
            }
        }
    </script>
</x-app-layout>
