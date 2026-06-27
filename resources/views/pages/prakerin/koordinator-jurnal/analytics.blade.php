<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Analisis Keaktifan Prakerin</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm"><p class="text-sm text-gray-500">Total Jurnal</p><p class="mt-2 text-2xl font-bold">{{ $summary['total_jurnal'] }}</p></div>
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm"><p class="text-sm text-gray-500">Menunggu</p><p class="mt-2 text-2xl font-bold text-yellow-600">{{ $summary['jurnal_menunggu'] }}</p></div>
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm"><p class="text-sm text-gray-500">Sudah Ditinjau</p><p class="mt-2 text-2xl font-bold text-emerald-600">{{ $summary['jurnal_ditinjau'] }}</p></div>
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm"><p class="text-sm text-gray-500">Total Absensi</p><p class="mt-2 text-2xl font-bold">{{ $summary['total_absensi'] }}</p></div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="font-bold text-gray-900">Keaktifan Siswa</h3>
                    <canvas id="studentActivityChart" class="mt-4 h-80"></canvas>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h3 class="font-bold text-gray-900">Pembimbing Paling Aktif</h3>
                    <canvas id="teacherActivityChart" class="mt-4 h-80"></canvas>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900">Data Apresiasi Pembimbing</h3>
                    <p class="text-sm text-gray-500">Urutan berdasarkan jumlah jurnal siswa yang sudah ditinjau.</p>
                </div>
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Pembimbing</th><th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Siswa Bimbingan</th><th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Jurnal Ditinjau</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($teacherActivity as $teacher)
                            <tr><td class="px-6 py-4 font-semibold">{{ $teacher->nama_lengkap }}</td><td class="px-6 py-4">{{ $teacher->siswa_count }}</td><td class="px-6 py-4">{{ $teacher->reviewed_jurnals_count }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const students = @json($studentActivity->map(fn ($row) => [
                    'name' => $row->siswa?->nama_lengkap ?? '-',
                    'jurnal' => $row->jurnals_count,
                    'absensi' => $row->absensis_count,
                ]));
                const teachers = @json($teacherActivity->map(fn ($row) => [
                    'name' => $row->nama_lengkap,
                    'reviewed' => $row->reviewed_jurnals_count,
                ]));

                new Chart(document.getElementById('studentActivityChart'), {
                    type: 'bar',
                    data: {
                        labels: students.map(row => row.name),
                        datasets: [
                            { label: 'Jurnal', data: students.map(row => row.jurnal), backgroundColor: '#dc2626', borderRadius: 8 },
                            { label: 'Absensi', data: students.map(row => row.absensi), backgroundColor: '#2563eb', borderRadius: 8 }
                        ]
                    },
                    options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
                });

                new Chart(document.getElementById('teacherActivityChart'), {
                    type: 'bar',
                    data: {
                        labels: teachers.map(row => row.name),
                        datasets: [{ label: 'Jurnal Ditinjau', data: teachers.map(row => row.reviewed), backgroundColor: '#059669', borderRadius: 8 }]
                    },
                    options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
                });
            });
        </script>
    @endpush
</x-app-layout>
