<x-app-layout>
    <x-slot name="header"><h2 class="font-bold text-xl text-gray-800">Report Penilaian</h2></x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <form method="GET" class="grid md:grid-cols-5 gap-3">
                <select name="period_id" class="rounded-lg border-gray-300 text-sm">
                    @foreach($periods as $item)
                        <option value="{{ $item->id }}" @selected($period?->id === $item->id)>{{ $item->title }}</option>
                    @endforeach
                </select>
                <select name="type" class="rounded-lg border-gray-300 text-sm">
                    <option value="all" @selected($type === 'all')>Semua Penilaian</option>
                    @foreach(\App\Models\AssessmentInstrument::TYPES as $key => $label)
                        <option value="{{ $key }}" @selected($type === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="target_kind" class="rounded-lg border-gray-300 text-sm">
                    <option value="teacher" @selected($targetKind === 'teacher')>Peringkat Guru</option>
                    <option value="student" @selected($targetKind === 'student')>Peringkat Siswa</option>
                </select>
                <button class="rounded-lg bg-gray-900 text-white text-sm font-bold px-4 py-2">Tampilkan</button>
                @if($period)
                    <a href="{{ route('penilaian.report.pdf', ['period_id' => $period->id, 'type' => $type, 'target_kind' => $targetKind]) }}" class="rounded-lg bg-red-600 text-white text-sm font-bold px-4 py-2 text-center">Unduh PDF</a>
                @endif
            </form>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            @foreach($summary as $label => $row)
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $label }}</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-2">{{ $row['average'] }}</p>
                    <p class="text-sm text-gray-500">{{ $row['responses'] }} respons</p>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="h-72"><canvas id="rankingChart"></canvas></div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Pemeringkatan {{ $targetKind === 'student' ? 'Siswa' : 'Guru' }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-5 py-3 text-left">Peringkat</th>
                            <th class="px-5 py-3 text-left">Nama</th>
                            <th class="px-5 py-3 text-left">Skor</th>
                            <th class="px-5 py-3 text-left">Respons</th>
                            <th class="px-5 py-3 text-left">Sertifikat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($ranking as $row)
                            <tr>
                                <td class="px-5 py-3 font-bold">#{{ $loop->iteration }}</td>
                                <td class="px-5 py-3">{{ $row['name'] }}</td>
                                <td class="px-5 py-3 font-bold text-red-700">{{ $row['score'] }}</td>
                                <td class="px-5 py-3">{{ $row['responses'] }}</td>
                                <td class="px-5 py-3">
                                    @if($loop->iteration <= 5 && $period)
                                        <a href="{{ route('penilaian.certificate', [$period, $targetKind, $row['id']]) }}" class="text-xs font-bold text-red-600">Unduh Sertifikat</a>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const labels = @json($ranking->take(10)->pluck('name'));
            const scores = @json($ranking->take(10)->pluck('score'));
            new Chart(document.getElementById('rankingChart'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Skor',
                        data: scores,
                        backgroundColor: 'rgba(220, 38, 38, 0.75)',
                        borderColor: 'rgb(185, 28, 28)',
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 700, easing: 'easeOutQuart' },
                    scales: { y: { beginAtZero: true, max: 100 } }
                }
            });
        </script>
    @endpush
</x-app-layout>
