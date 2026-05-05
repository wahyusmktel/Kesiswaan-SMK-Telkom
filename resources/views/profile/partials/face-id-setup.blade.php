<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Pengaturan Face ID') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Daftarkan wajah Anda untuk bisa login tanpa memasukkan password.') }}
        </p>
    </header>

    @if (session('status') === 'face-id-updated')
        <div class="mt-4 p-4 bg-emerald-50 rounded-xl border border-emerald-200" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
            <p class="text-sm font-medium text-emerald-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Face ID berhasil diperbarui.
            </p>
        </div>
    @endif
    
    @if ($errors->has('face_image'))
        <div class="mt-4 p-4 bg-red-50 rounded-xl border border-red-200">
            <p class="text-sm font-medium text-red-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                {{ $errors->first('face_image') }}
            </p>
        </div>
    @endif

    <div class="mt-6 space-y-6" x-data="faceIdSetup()">
        
        @if(auth()->user()->face_photo)
            <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-start gap-4">
                <div class="w-16 h-16 rounded-lg overflow-hidden border-2 border-white shadow-md flex-shrink-0">
                    <img src="{{ asset('storage/' . auth()->user()->face_photo) }}" alt="Face ID Terdaftar" class="w-full h-full object-cover">
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Face ID Sudah Terdaftar</h3>
                    <p class="text-sm text-gray-600 mt-1">Anda sudah dapat login menggunakan wajah Anda. Ingin memperbaruinya?</p>
                </div>
            </div>
        @else
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                <p class="text-sm text-gray-600 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Belum ada Face ID yang terdaftar.
                </p>
            </div>
        @endif

        {{-- Registration UI --}}
        <div x-show="!isCapturing" class="flex justify-center">
            <button type="button" @click="startRegistration()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ auth()->user()->face_photo ? 'Perbarui Face ID' : 'Daftarkan Face ID' }}
            </button>
        </div>

        <div x-show="isCapturing" style="display:none;" class="border border-gray-200 rounded-2xl p-6 bg-gray-50">
            <div class="relative w-full max-w-sm mx-auto aspect-square overflow-hidden rounded-full border-4 border-blue-500 shadow-xl bg-black">
                <video x-ref="video" autoplay playsinline muted class="w-full h-full object-cover" style="transform: scaleX(-1);"></video>
                <div class="absolute inset-0 border-8 border-transparent border-t-blue-500/50 border-b-blue-500/50 rounded-full animate-spin pointer-events-none" style="animation-duration: 3s;"></div>
            </div>
            
            <p class="text-center text-sm font-bold text-gray-700 mt-6" x-text="statusText"></p>
            
            <div class="mt-6 flex items-center justify-center gap-3">
                <button type="button" @click="captureAndSubmit()" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-md">
                    Ambil Foto & Simpan
                </button>
                <button type="button" @click="cancelRegistration()" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold rounded-xl transition-all shadow-sm">
                    Batal
                </button>
            </div>
            
            <canvas x-ref="canvas" style="display:none;"></canvas>
            
            <form id="face-form" action="{{ route('profile.face.update') }}" method="POST" class="hidden">
                @csrf
                @method('PATCH')
                <input type="hidden" name="face_image" x-ref="faceInput">
            </form>
        </div>

    </div>

    <script>
        function faceIdSetup() {
            return {
                isCapturing: false,
                stream: null,
                statusText: 'Posisikan wajah Anda dengan jelas di tengah',
                
                async startRegistration() {
                    this.isCapturing = true;
                    this.statusText = 'Menghubungkan ke kamera...';
                    
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 640 } },
                            audio: false
                        });
                        this.$refs.video.srcObject = this.stream;
                        this.statusText = 'Posisikan wajah Anda dengan jelas di tengah';
                    } catch (err) {
                        this.statusText = 'Kamera tidak dapat diakses. Pastikan Anda telah memberikan izin.';
                        setTimeout(() => this.cancelRegistration(), 3000);
                    }
                },
                
                cancelRegistration() {
                    this.isCapturing = false;
                    if (this.stream) {
                        this.stream.getTracks().forEach(t => t.stop());
                        this.stream = null;
                        this.$refs.video.srcObject = null;
                    }
                },
                
                captureAndSubmit() {
                    if (!this.stream) return;
                    
                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    const ctx = canvas.getContext('2d');
                    
                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 640;
                    
                    // Mirror the image to save it correctly
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, 0, 0);
                    
                    // Get base64 data
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                    this.$refs.faceInput.value = dataUrl;
                    
                    this.statusText = 'Menyimpan Face ID...';
                    
                    // Stop camera
                    this.stream.getTracks().forEach(t => t.stop());
                    
                    // Submit form
                    document.getElementById('face-form').submit();
                }
            }
        }
    </script>
</section>
