<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.berita.index') }}"
                class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-lg font-bold text-gray-800 leading-tight">Moderasi Komentar Berita</h2>
                <p class="text-xs text-gray-500">Tinjau komentar dan balasan sebelum ditampilkan ke publik</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach ([
                'pending' => ['Menunggu', 'amber'],
                'approved' => ['Disetujui', 'emerald'],
                'rejected' => ['Ditolak', 'red'],
            ] as $status => [$label, $color])
                <a href="{{ route('super-admin.berita-comments.index', ['status' => $status]) }}"
                    class="bg-white border rounded-xl p-4 transition-colors {{ $selectedStatus === $status ? 'border-red-300 ring-2 ring-red-50' : 'border-gray-100 hover:border-gray-200' }}">
                    <span class="text-xs font-bold text-gray-500">{{ $label }}</span>
                    <strong class="block text-2xl text-gray-900 mt-1">{{ number_format($counts[$status] ?? 0) }}</strong>
                </a>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Pengirim</th>
                            <th class="px-5 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Komentar</th>
                            <th class="px-5 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Berita</th>
                            <th class="px-5 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($comments as $comment)
                            <tr class="align-top hover:bg-gray-50/60">
                                <td class="px-5 py-4 min-w-44">
                                    <p class="text-sm font-bold text-gray-900">{{ $comment->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $comment->email }}</p>
                                    <p class="text-[10px] text-gray-400 mt-2">{{ $comment->created_at->format('d M Y H:i') }}</p>
                                </td>
                                <td class="px-5 py-4 min-w-80 max-w-xl">
                                    @if ($comment->parent)
                                        <span class="inline-flex mb-2 px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold">
                                            Balasan untuk {{ $comment->parent->name }}
                                        </span>
                                    @endif
                                    <p class="text-sm text-gray-700 leading-6 whitespace-pre-line">{{ $comment->content }}</p>
                                </td>
                                <td class="px-5 py-4 min-w-52">
                                    <a href="{{ route('berita.show', $comment->berita->slug) }}" target="_blank"
                                        class="text-sm font-semibold text-gray-800 hover:text-red-600 line-clamp-2">
                                        {{ $comment->berita->judul }}
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($comment->status !== 'approved')
                                            <form method="POST" action="{{ route('super-admin.berita-comments.moderate', $comment) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" title="Setujui"
                                                    class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($comment->status !== 'rejected')
                                            <form method="POST" action="{{ route('super-admin.berita-comments.moderate', $comment) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" title="Tolak"
                                                    class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('super-admin.berita-comments.destroy', $comment) }}"
                                            onsubmit="return confirm('Hapus komentar ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7 18.1 19.1A2 2 0 0 1 16.1 21H7.9a2 2 0 0 1-2-1.9L5 7m5 4v6m4-6v6m1-10V4H9v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center text-sm text-gray-400">
                                    Tidak ada komentar pada status ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($comments->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $comments->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
