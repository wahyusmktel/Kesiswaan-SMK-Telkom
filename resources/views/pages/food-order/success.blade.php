<x-app-layout>
    <div class="py-12 w-full flex items-center justify-center min-h-[80vh]">
        <div class="w-full max-w-lg px-4">
            <div class="bg-white rounded-[32px] p-8 md:p-12 shadow-2xl border border-gray-100 text-center relative overflow-hidden">
                
                {{-- Decorative background elements --}}
                <div class="absolute -top-16 -right-16 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>

                <div class="relative z-10">
                    <div class="w-24 h-24 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    
                    <h2 class="text-3xl font-black text-gray-900 mb-2">Pesanan Dibuat!</h2>
                    <p class="text-gray-500 font-medium mb-8">Kantin sedang memproses pesananmu. Nomor order kamu adalah:</p>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 mb-8">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Order ID</p>
                        <p class="text-2xl font-black text-gray-900 tracking-wider">{{ $order->order_number }}</p>
                    </div>

                    <div class="space-y-4">
                        <a href="{{ route('food-order.history') }}" class="w-full block bg-gray-900 hover:bg-black text-white font-black py-4 rounded-xl shadow-xl shadow-gray-900/20 transition-transform active:scale-95">Lihat Status Pesanan</a>
                        
                        <a href="{{ route('food-order.index') }}" class="w-full block bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold py-4 rounded-xl transition-colors">Pesan Makanan Lain</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
