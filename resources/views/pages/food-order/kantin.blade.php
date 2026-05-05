<x-app-layout>
    <div x-data="cartSystem({{ $kantin->user_id }})" class="relative h-screen flex flex-col w-full bg-gray-50 overflow-hidden">
        
        {{-- Kantin Header Banner --}}
        <div class="h-48 md:h-64 bg-gray-900 relative flex-shrink-0 w-full">
            @if($kantin->banner_image)
                <img src="{{ asset('storage/' . $kantin->banner_image) }}" class="w-full h-full object-cover opacity-60">
            @else
                <div class="w-full h-full bg-gradient-to-r from-red-600 to-orange-500 opacity-80"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
            
            <div class="absolute top-4 left-4">
                <a href="{{ route('food-order.index') }}" class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white hover:bg-white/40 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            </div>

            <div class="absolute bottom-6 left-6 right-6 md:left-12 md:right-12 text-white">
                <h1 class="text-3xl md:text-4xl font-black mb-1 drop-shadow-md">{{ $kantin->name ?? 'Kantin ' . $kantin->user->name }}</h1>
                <p class="text-gray-200 text-sm md:text-base max-w-2xl drop-shadow">{{ $kantin->description }}</p>
                @if($kantin->phone_number)
                <div class="mt-2 flex items-center gap-2 text-xs font-bold text-white/80">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $kantin->phone_number }}
                </div>
                @endif
            </div>
        </div>

        {{-- Main Content & Cart Layout --}}
        <div class="flex-1 flex overflow-hidden w-full">
            
            {{-- Menu List (Scrollable) --}}
            <div class="flex-1 overflow-y-auto p-4 md:p-8 sidebar-scroll w-full pb-32 lg:pb-8">
                
                @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
                @endif

                @forelse($menus as $category => $items)
                <div class="mb-10">
                    <h2 class="text-xl font-black text-gray-900 mb-6 uppercase tracking-wider sticky top-0 bg-gray-50/90 backdrop-blur-md py-2 z-10 border-b border-gray-200">{{ $category }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($items as $item)
                        <div class="bg-white rounded-2xl p-4 flex gap-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            
                            {{-- Image Carousel if multiple images exist --}}
                            <div class="w-24 h-24 md:w-32 md:h-32 rounded-xl bg-gray-100 flex-shrink-0 overflow-hidden relative" x-data="{ activeSlide: 0, slides: {{ json_encode($item->images ?? []) }} }">
                                @if($item->images && count($item->images) > 0)
                                    <template x-for="(slide, index) in slides" :key="index">
                                        <img :src="'/storage/' + slide" x-show="activeSlide === index" class="w-full h-full object-cover absolute inset-0 transition-opacity duration-300">
                                    </template>
                                    
                                    {{-- Carousel Controls if > 1 image --}}
                                    @if(count($item->images) > 1)
                                    <button @click.prevent="activeSlide = activeSlide === 0 ? slides.length - 1 : activeSlide - 1" class="absolute left-1 top-1/2 -translate-y-1/2 w-6 h-6 bg-black/50 text-white rounded-full flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity" style="backdrop-filter: blur(4px);">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <button @click.prevent="activeSlide = activeSlide === slides.length - 1 ? 0 : activeSlide + 1" class="absolute right-1 top-1/2 -translate-y-1/2 w-6 h-6 bg-black/50 text-white rounded-full flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity" style="backdrop-filter: blur(4px);">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                    {{-- Dots --}}
                                    <div class="absolute bottom-1 left-0 right-0 flex justify-center gap-1">
                                        <template x-for="(slide, index) in slides" :key="index">
                                            <div class="w-1.5 h-1.5 rounded-full" :class="activeSlide === index ? 'bg-white' : 'bg-white/50'"></div>
                                        </template>
                                    </div>
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 flex flex-col justify-center">
                                <h4 class="font-bold text-gray-900 leading-tight mb-1">{{ $item->name }}</h4>
                                <p class="text-xs text-gray-500 line-clamp-2 mb-2">{{ $item->description }}</p>
                                <div class="flex items-center justify-between mt-auto">
                                    <span class="font-black text-orange-600">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                    
                                    {{-- Add to Cart Buttons --}}
                                    <div class="flex items-center">
                                        <button x-show="getItemQuantity({{ $item->id }}) === 0" @click="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})" class="border-2 border-orange-500 text-orange-500 hover:bg-orange-50 font-bold px-4 py-1.5 rounded-full text-xs transition-colors">Tambah</button>
                                        
                                        <div x-show="getItemQuantity({{ $item->id }}) > 0" class="flex items-center gap-3 bg-gray-50 rounded-full border border-gray-200 px-1 py-1" style="display: none;">
                                            <button @click="removeFromCart({{ $item->id }})" class="w-6 h-6 bg-white border border-gray-200 rounded-full flex items-center justify-center text-orange-500 hover:bg-gray-100">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                            </button>
                                            <span class="font-black text-sm w-4 text-center" x-text="getItemQuantity({{ $item->id }})"></span>
                                            <button @click="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})" class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-white hover:bg-orange-600 shadow-md shadow-orange-500/20">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="py-20 text-center text-gray-500 font-bold">Kantin ini belum memiliki menu yang tersedia.</div>
                @endforelse
            </div>

            {{-- Desktop Cart Sidebar --}}
            <div class="hidden lg:flex w-96 bg-white border-l border-gray-200 flex-col relative z-20 shadow-2xl">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-xl font-black text-gray-900">Pesananmu</h3>
                    <p class="text-sm text-gray-500" x-text="kantinName"></p>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 sidebar-scroll bg-gray-50/30">
                    <template x-if="cart.length === 0">
                        <div class="h-full flex flex-col items-center justify-center text-center opacity-50">
                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <p class="font-bold text-gray-600">Keranjang masih kosong</p>
                            <p class="text-xs text-gray-500">Pilih menu favoritmu sekarang!</p>
                        </div>
                    </template>
                    
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="flex gap-3 mb-4 bg-white p-3 rounded-xl border border-gray-100 shadow-sm relative group">
                            <div class="flex-1">
                                <h5 class="font-bold text-sm text-gray-900 leading-tight mb-1" x-text="item.name"></h5>
                                <p class="text-xs font-black text-orange-500 mb-2">Rp <span x-text="formatNumber(item.price)"></span></p>
                            </div>
                            <div class="flex flex-col items-end justify-between">
                                <p class="text-sm font-black text-gray-900">Rp <span x-text="formatNumber(item.price * item.quantity)"></span></p>
                                <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-1 py-1">
                                    <button @click="removeFromCart(item.id)" class="w-5 h-5 bg-white border border-gray-200 rounded flex items-center justify-center text-orange-500">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <span class="font-bold text-xs w-3 text-center" x-text="item.quantity"></span>
                                    <button @click="addToCart(item.id, item.name, item.price)" class="w-5 h-5 bg-orange-500 rounded flex items-center justify-center text-white">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Checkout Form --}}
                <div x-show="cart.length > 0" class="p-6 bg-white border-t border-gray-100 shadow-[0_-10px_40px_rgba(0,0,0,0.05)]" style="display: none;">
                    <form action="{{ route('food-order.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kantin_id" value="{{ $kantin->user_id }}">
                        <input type="hidden" name="items" :value="JSON.stringify(cart)">
                        
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pilih Pembayaran</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="border rounded-xl p-3 cursor-pointer relative" :class="paymentMethod == 'cash' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
                                    <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only">
                                    <div class="font-bold text-sm text-center">Tunai</div>
                                    <svg x-show="paymentMethod == 'cash'" class="w-4 h-4 text-orange-500 absolute top-1 right-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </label>
                                <label class="border rounded-xl p-3 cursor-pointer relative" :class="paymentMethod == 'qris' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
                                    <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="sr-only">
                                    <div class="font-bold text-sm text-center text-blue-600">QRIS</div>
                                    <svg x-show="paymentMethod == 'qris'" class="w-4 h-4 text-orange-500 absolute top-1 right-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Catatan Tambahan</label>
                            <input type="text" name="notes" class="w-full text-sm bg-gray-50 border border-gray-200 rounded-lg p-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Cth: Jangan pedes ya bg..">
                        </div>

                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm font-bold text-gray-600">Total Harga</span>
                            <span class="text-2xl font-black text-gray-900">Rp <span x-text="formatNumber(cartTotal)"></span></span>
                        </div>

                        <button type="submit" @click="clearCartOnSubmit()" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-4 rounded-xl shadow-xl shadow-orange-500/30 transition-transform active:scale-95 flex items-center justify-center gap-2">
                            <span>Pesan Sekarang</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Mobile Cart Float Bar (Appears when cart > 0) --}}
            <div x-show="cart.length > 0" class="lg:hidden fixed bottom-0 left-0 right-0 p-4 z-50 bg-white border-t border-gray-200 shadow-[0_-10px_30px_rgba(0,0,0,0.1)] flex justify-between items-center" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="transform translate-y-full" x-transition:enter-end="transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="transform translate-y-0" x-transition:leave-end="transform translate-y-full">
                <div class="flex-1">
                    <div class="text-xs font-bold text-gray-500 mb-0.5"><span x-text="cartItemCount"></span> Item Tersimpan</div>
                    <div class="text-xl font-black text-orange-600">Rp <span x-text="formatNumber(cartTotal)"></span></div>
                </div>
                <button @click="$dispatch('open-mobile-cart')" class="bg-gray-900 text-white font-black px-6 py-3 rounded-full flex items-center gap-2 shadow-lg active:scale-95 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Lihat Pesanan
                </button>
            </div>
            
        </div>

        {{-- Mobile Cart Modal --}}
        <div x-data="{ mobileCartOpen: false }" @open-mobile-cart.window="mobileCartOpen = true" x-show="mobileCartOpen" class="fixed inset-0 z-[100] lg:hidden" style="display: none;">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="mobileCartOpen = false" x-transition.opacity></div>
            <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-h-[90vh] flex flex-col" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="transform translate-y-full" x-transition:enter-end="transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="transform translate-y-0" x-transition:leave-end="transform translate-y-full">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-black text-gray-900">Pesananmu</h3>
                    <button @click="mobileCartOpen = false" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="flex gap-3 mb-3 bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex-1">
                                <h5 class="font-bold text-sm text-gray-900 mb-1" x-text="item.name"></h5>
                                <p class="text-xs font-black text-orange-500">Rp <span x-text="formatNumber(item.price)"></span></p>
                            </div>
                            <div class="flex flex-col items-end justify-between">
                                <p class="text-sm font-black text-gray-900">Rp <span x-text="formatNumber(item.price * item.quantity)"></span></p>
                                <div class="flex items-center gap-2 mt-2">
                                    <button @click="removeFromCart(item.id)" class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center text-orange-500 font-bold">-</button>
                                    <span class="font-bold text-xs w-4 text-center" x-text="item.quantity"></span>
                                    <button @click="addToCart(item.id, item.name, item.price)" class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold">+</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="p-4 bg-white border-t border-gray-100 shadow-[0_-10px_20px_rgba(0,0,0,0.05)]">
                    <form action="{{ route('food-order.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kantin_id" value="{{ $kantin->user_id }}">
                        <input type="hidden" name="items" :value="JSON.stringify(cart)">
                        
                        <div class="mb-4 grid grid-cols-2 gap-2">
                            <label class="border rounded-xl p-2 cursor-pointer relative" :class="paymentMethod == 'cash' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
                                <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only">
                                <div class="font-bold text-xs text-center">Tunai</div>
                            </label>
                            <label class="border rounded-xl p-2 cursor-pointer relative" :class="paymentMethod == 'qris' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
                                <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="sr-only">
                                <div class="font-bold text-xs text-center text-blue-600">QRIS</div>
                            </label>
                        </div>
                        
                        <input type="text" name="notes" class="w-full text-xs bg-gray-50 border border-gray-200 rounded-lg p-2 mb-4" placeholder="Catatan tambahan (opsional)">

                        <button type="submit" @click="clearCartOnSubmit()" class="w-full bg-gray-900 text-white font-black py-4 rounded-xl flex items-center justify-between px-6 active:scale-95 transition-transform">
                            <span>Pesan & Bayar</span>
                            <span>Rp <span x-text="formatNumber(cartTotal)"></span></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cartSystem', (kantinId) => ({
                kantinId: kantinId,
                kantinName: '{{ $kantin->name ?? 'Kantin ' . $kantin->user->name }}',
                cart: JSON.parse(localStorage.getItem('kantin_cart_' + kantinId)) || [],
                paymentMethod: 'cash',
                
                get cartTotal() {
                    return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
                },
                
                get cartItemCount() {
                    return this.cart.reduce((count, item) => count + item.quantity, 0);
                },

                getItemQuantity(id) {
                    const item = this.cart.find(i => i.id === id);
                    return item ? item.quantity : 0;
                },

                addToCart(id, name, price) {
                    const existing = this.cart.find(i => i.id === id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.cart.push({ id, name, price, quantity: 1 });
                    }
                    this.saveCart();
                },

                removeFromCart(id) {
                    const existingIndex = this.cart.findIndex(i => i.id === id);
                    if (existingIndex !== -1) {
                        if (this.cart[existingIndex].quantity > 1) {
                            this.cart[existingIndex].quantity--;
                        } else {
                            this.cart.splice(existingIndex, 1);
                        }
                        this.saveCart();
                    }
                },

                saveCart() {
                    localStorage.setItem('kantin_cart_' + this.kantinId, JSON.stringify(this.cart));
                },

                clearCartOnSubmit() {
                    // Small delay so form submission catches the payload before it's cleared
                    setTimeout(() => {
                        this.cart = [];
                        this.saveCart();
                    }, 500);
                },
                
                formatNumber(num) {
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            }));
        });
    </script>
    <style>
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    </style>
</x-app-layout>
