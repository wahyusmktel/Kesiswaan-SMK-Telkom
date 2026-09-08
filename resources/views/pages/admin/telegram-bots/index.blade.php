<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold text-gray-800">Telegram Bot Gateway</h2></x-slot>
    <div class="mx-auto max-w-7xl space-y-6 p-6">
        <section class="rounded-3xl bg-gradient-to-r from-sky-700 to-blue-900 p-7 text-white shadow-xl">
            <p class="text-xs font-bold uppercase tracking-widest text-sky-200">Multi-bot notification gateway</p>
            <h1 class="mt-2 text-3xl font-black">Telegram Bot SISFO</h1>
            <p class="mt-2 max-w-3xl text-sm text-sky-100">Kelola bot berdasarkan keperluan. Bot pertama direkomendasikan bernama <strong>HC SMK Telkom Lampung</strong> dengan fungsi Kepegawaian. Token disimpan terenkripsi.</p>
        </section>
        @if(session('success'))<div class="rounded-xl bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="rounded-xl bg-red-50 p-4 text-red-800">{{ session('error') }}</div>@endif
        @if($errors->any())<div class="rounded-xl bg-red-50 p-4 text-red-800">{{ $errors->first() }}</div>@endif

        <section class="rounded-2xl border bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold">Tambah Bot Telegram</h3>
            <form method="POST" action="{{ route('super-admin.telegram-bots.store') }}" class="mt-4 grid gap-4 md:grid-cols-2">
                @csrf
                <label class="text-sm font-semibold">Nama Bot<input name="name" required maxlength="100" value="{{ old('name', 'HC SMK Telkom Lampung') }}" class="mt-1 block w-full rounded-xl border-gray-300"></label>
                <label class="text-sm font-semibold">Kode Bot<input name="slug" required maxlength="60" value="{{ old('slug', 'hc-kepegawaian') }}" class="mt-1 block w-full rounded-xl border-gray-300"></label>
                <label class="text-sm font-semibold">Keperluan<select name="purpose" class="mt-1 block w-full rounded-xl border-gray-300"><option value="employment">Kepegawaian</option><option value="student_affairs">Kesiswaan</option><option value="general">Umum</option></select></label>
                <label class="text-sm font-semibold">Token Bot dari BotFather<input type="password" name="bot_token" required autocomplete="new-password" class="mt-1 block w-full rounded-xl border-gray-300" placeholder="123456789:AA..."></label>
                <label class="flex items-center gap-2 text-sm font-semibold"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-sky-600"> Aktifkan bot</label>
                <div class="md:text-right"><button class="rounded-xl bg-sky-600 px-5 py-2.5 font-bold text-white">Simpan & Aktifkan Webhook</button></div>
            </form>
        </section>

        <section class="grid gap-5 lg:grid-cols-2">
            @forelse($bots as $bot)
                <article class="rounded-2xl border bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-3"><div><h3 class="text-lg font-black">{{ $bot->name }}</h3><p class="text-sm text-gray-500">{{ '@'.($bot->bot_username ?: 'belum terverifikasi') }} · {{ $bot->purpose }}</p></div><span class="rounded-full px-3 py-1 text-xs font-bold {{ $bot->status === 'connected' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">{{ $bot->status }}</span></div>
                    <p class="mt-3 text-sm"><strong>{{ $bot->user_links_count }}</strong> akun pegawai terhubung</p>
                    @if($bot->bot_username)<a target="_blank" rel="noopener" href="https://t.me/{{ $bot->bot_username }}?start=sisfo" class="mt-3 inline-block text-sm font-bold text-sky-700">Buka bot & bagikan ke pegawai ↗</a>@endif
                    @if($bot->last_error)<p class="mt-3 rounded-lg bg-red-50 p-3 text-xs text-red-700">{{ $bot->last_error }}</p>@endif
                    <form method="POST" action="{{ route('super-admin.telegram-bots.update', $bot) }}" class="mt-5 grid gap-3 sm:grid-cols-2">
                        @csrf @method('PUT')
                        <input name="name" value="{{ $bot->name }}" required class="rounded-lg border-gray-300 text-sm"><input name="slug" value="{{ $bot->slug }}" required class="rounded-lg border-gray-300 text-sm">
                        <select name="purpose" class="rounded-lg border-gray-300 text-sm"><option value="employment" @selected($bot->purpose === 'employment')>Kepegawaian</option><option value="student_affairs" @selected($bot->purpose === 'student_affairs')>Kesiswaan</option><option value="general" @selected($bot->purpose === 'general')>Umum</option></select>
                        <input type="password" name="bot_token" autocomplete="new-password" placeholder="Token baru (opsional)" class="rounded-lg border-gray-300 text-sm">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($bot->is_active) class="rounded"> Aktif</label><button class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-bold text-white">Simpan</button>
                    </form>
                    <div class="mt-3 flex gap-2"><form method="POST" action="{{ route('super-admin.telegram-bots.verify', $bot) }}">@csrf<button class="rounded-lg border px-3 py-2 text-sm font-semibold">Verifikasi ulang</button></form><form method="POST" action="{{ route('super-admin.telegram-bots.destroy', $bot) }}" onsubmit="return confirm('Hapus bot dan seluruh hubungan akun Telegram?');">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700">Hapus</button></form></div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed bg-white p-8 text-center text-gray-500 lg:col-span-2">Belum ada bot Telegram. Tambahkan HC SMK Telkom Lampung menggunakan token dari BotFather.</div>
            @endforelse
        </section>

        <section class="rounded-2xl border bg-white p-6 shadow-sm">
            <h3 class="font-bold">Akun Telegram Terhubung</h3><p class="mt-1 text-sm text-gray-500">Pegawai membuka bot, menekan Start, lalu membagikan nomor HP miliknya. Nomor harus sama dengan kolom HP Dapodik Guru, dan data guru harus sudah terhubung ke akun SISFO.</p>
            <div class="mt-4 overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-gray-50"><tr><th class="p-3">Pegawai</th><th class="p-3">Bot</th><th class="p-3">Telegram</th><th class="p-3">Terhubung</th><th class="p-3">Aksi</th></tr></thead><tbody class="divide-y">
                @forelse($links as $link)<tr><td class="p-3 font-semibold">{{ $link->user?->name }}</td><td class="p-3">{{ $link->bot?->name }}</td><td class="p-3">{{ $link->telegram_username ? '@'.$link->telegram_username : $link->telegram_name }}</td><td class="p-3">{{ $link->linked_at?->format('d/m/Y H:i') }}</td><td class="p-3"><form method="POST" action="{{ route('super-admin.telegram-links.destroy', $link) }}" onsubmit="return confirm('Lepas hubungan akun Telegram ini?');">@csrf @method('DELETE')<button class="text-xs font-bold text-red-700">Lepaskan</button></form></td></tr>@empty<tr><td colspan="5" class="p-6 text-center text-gray-500">Belum ada akun yang terhubung.</td></tr>@endforelse
            </tbody></table></div><div class="mt-4">{{ $links->links() }}</div>
        </section>

        <section class="rounded-2xl border bg-white p-6 shadow-sm">
            <h3 class="font-bold">Log Pengiriman Telegram Terbaru</h3>
            <div class="mt-4 overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-gray-50"><tr><th class="p-3">Waktu</th><th class="p-3">Bot</th><th class="p-3">Penerima</th><th class="p-3">Jenis</th><th class="p-3">Status</th></tr></thead><tbody class="divide-y">@forelse($logs as $log)<tr><td class="p-3">{{ $log->created_at?->format('d/m H:i') }}</td><td class="p-3">{{ $log->bot?->name ?: '-' }}</td><td class="p-3">{{ $log->recipient_name ?: $log->chat_id }}</td><td class="p-3">{{ $log->type }}</td><td class="p-3"><span class="font-bold {{ $log->status === 'sent' ? 'text-emerald-700' : ($log->status === 'failed' ? 'text-red-700' : 'text-amber-700') }}">{{ $log->status }}</span>@if($log->error_message)<span class="block max-w-md text-xs text-red-600">{{ $log->error_message }}</span>@endif</td></tr>@empty<tr><td colspan="5" class="p-6 text-center text-gray-500">Belum ada pengiriman Telegram.</td></tr>@endforelse</tbody></table></div>
        </section>
    </div>
</x-app-layout>
