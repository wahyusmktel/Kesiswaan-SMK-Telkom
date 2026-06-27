<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Konsultasi Bimbingan Prakerin</h2>
    </x-slot>

    @php
        $displayName = fn ($user) => $user?->masterSiswa?->nama_lengkap ?? $user?->masterGuru?->nama_lengkap ?? $user?->name ?? '-';
        $isPrivate = (bool) $receiver;
    @endphp

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
                <p class="font-bold">Panduan konsultasi</p>
                <p class="mt-1">Gunakan Forum Rombel untuk diskusi umum bersama peserta prakerin satu rombel. Gunakan Private Chat untuk bimbingan personal antara siswa dan pembimbing internal.</p>
            </div>

            <div class="grid gap-6 xl:grid-cols-[280px_1fr]">
                <aside class="space-y-4">
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                        <h3 class="font-bold text-gray-900">Rombel</h3>
                        <div class="mt-3 space-y-2">
                            @foreach($rombels as $rombel)
                                <a href="{{ route(request()->routeIs('siswa.*') ? 'siswa.prakerin-konsultasi.index' : 'pembimbing-prakerin.konsultasi.index', ['rombel_id' => $rombel->id]) }}"
                                    class="block rounded-xl px-3 py-2 text-sm font-semibold {{ $activeRombel->id === $rombel->id ? 'bg-red-600 text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">
                                    {{ $rombel->nama_rombel }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                        <h3 class="font-bold text-gray-900">Mode Chat</h3>
                        <a href="{{ route(request()->routeIs('siswa.*') ? 'siswa.prakerin-konsultasi.index' : 'pembimbing-prakerin.konsultasi.index', ['rombel_id' => $activeRombel->id]) }}"
                            class="mt-3 block rounded-xl px-3 py-2 text-sm font-semibold {{ ! $isPrivate ? 'bg-emerald-600 text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">
                            Forum Rombel
                        </a>
                        <p class="mt-4 text-xs font-bold uppercase text-gray-500">Private Chat</p>
                        <div class="mt-2 space-y-2">
                            @forelse($participants as $participant)
                                <a href="{{ route(request()->routeIs('siswa.*') ? 'siswa.prakerin-konsultasi.index' : 'pembimbing-prakerin.konsultasi.index', ['rombel_id' => $activeRombel->id, 'receiver_id' => $participant->id]) }}"
                                    class="block rounded-xl px-3 py-2 text-sm font-semibold {{ $receiver?->id === $participant->id ? 'bg-gray-900 text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">
                                    {{ $displayName($participant) }}
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada peserta lain.</p>
                            @endforelse
                        </div>
                    </div>
                </aside>

                <section class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="border-b border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900">{{ $isPrivate ? 'Private Chat dengan ' . $displayName($receiver) : 'Forum Diskusi ' . $activeRombel->nama_rombel }}</h3>
                        <p class="text-sm text-gray-500">{{ $isPrivate ? 'Percakapan personal untuk konsultasi bimbingan.' : 'Diskusi umum seluruh peserta prakerin dalam satu rombel.' }}</p>
                    </div>

                    <div class="max-h-[560px] space-y-4 overflow-y-auto bg-gray-50 p-6">
                        @forelse($messages as $message)
                            @php($mine = $message->sender_id === auth()->id())
                            <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[78%] rounded-2xl px-4 py-3 shadow-sm {{ $mine ? 'bg-red-600 text-white' : 'bg-white text-gray-800' }}">
                                    <p class="text-xs font-bold {{ $mine ? 'text-red-100' : 'text-gray-500' }}">{{ $mine ? 'Saya' : $displayName($message->sender) }}</p>
                                    <p class="mt-1 whitespace-pre-line text-sm">{{ $message->message }}</p>
                                    <p class="mt-2 text-right text-[11px] {{ $mine ? 'text-red-100' : 'text-gray-400' }}">{{ $message->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="rounded-xl bg-white p-8 text-center text-gray-500">Belum ada pesan. Mulai diskusi dengan pesan pertama.</p>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route(request()->routeIs('siswa.*') ? 'siswa.prakerin-konsultasi.store' : 'pembimbing-prakerin.konsultasi.store', ['rombel_id' => $activeRombel->id, 'receiver_id' => $receiver?->id]) }}" class="border-t border-gray-100 p-5">
                        @csrf
                        <input type="hidden" name="type" value="{{ $isPrivate ? 'private' : 'group' }}">
                        @if($receiver)
                            <input type="hidden" name="receiver_id" value="{{ $receiver->id }}">
                        @endif
                        <div class="flex gap-3">
                            <textarea name="message" rows="2" class="flex-1 rounded-xl border-gray-200" placeholder="Tulis pesan konsultasi..." required></textarea>
                            <button class="rounded-xl bg-red-600 px-5 py-2 font-semibold text-white hover:bg-red-700">Kirim</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
