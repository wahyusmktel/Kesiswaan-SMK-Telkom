<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Kelola Jadwal Pelajaran</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-700 text-lg">Pilih Rombongan Belajar</h3>
                    <p class="text-sm text-gray-500">Klik pada kartu kelas untuk mulai mengatur jadwal.</p>
                </div>

                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($rombels as $rombel)
                        <a href="{{ route('kurikulum.jadwal-pelajaran.show', $rombel->id) }}"
                            class="group relative bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-300">

                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors shadow-sm">
                                    {{ substr($rombel->kelas->nama_kelas, 0, 1) }}
                                </div>
                                <span
                                    class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-bold border border-gray-200">
                                    {{ $rombel->tahun_ajaran }}
                                </span>
                            </div>

                            <h4 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">
                                {{ $rombel->kelas->nama_kelas }}
                            </h4>

                            <div class="mt-3 flex items-center text-sm text-gray-500 border-t border-gray-50 pt-3">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $rombel->waliKelas->name ?? 'Belum ada Wali' }}
                            </div>

                            <div
                                class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity transform translate-x-2 group-hover:translate-x-0">
                                <svg class="w-6 h-6 text-indigo-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
