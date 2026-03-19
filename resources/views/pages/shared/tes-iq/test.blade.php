<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pertanyaan Tes IQ') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen" x-data="{ currentQ: 0, total: {{ $questions->count() }} }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Progress Bar -->
                <div class="w-full bg-gray-100 h-2">
                    <div class="bg-indigo-600 h-2 transition-all duration-500 ease-out" 
                         :style="'width: ' + ((currentQ + 1) / total * 100) + '%'"></div>
                </div>

                <div class="p-8 sm:p-12">
                    <form action="{{ route('tes-iq.submit') }}" method="POST" id="iqTestForm">
                        @csrf
                        
                        @foreach($questions as $index => $q)
                        <div x-show="currentQ === {{ $index }}" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 mb-6">
                                Pertanyaan {{ $index + 1 }} dari {{ $questions->count() }}
                            </span>
                            
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 leading-snug mb-8">
                                {{ $q->question_text }}
                            </h3>

                            @if($q->image_path)
                                <img src="{{ asset($q->image_path) }}" alt="Pertanyaan" class="mb-8 rounded-xl border border-gray-200 shadow-sm max-w-full h-auto">
                            @endif

                            <div class="space-y-4">
                                @foreach(['A', 'B', 'C', 'D'] as $opt)
                                    @php $optField = 'option_' . strtolower($opt); @endphp
                                    <label class="relative flex cursor-pointer rounded-2xl border bg-white p-4 shadow-sm focus:outline-none hover:border-indigo-300 hover:bg-indigo-50 transition-all border-gray-200">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}" class="peer sr-only">
                                        <span class="flex flex-1 items-center">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-gray-900 ml-8">{{ $q->$optField }}</span>
                                            </span>
                                        </span>
                                        <!-- Checkbox Circle Indicator -->
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 rounded-full border border-gray-300 flex items-center justify-center peer-checked:border-indigo-600 peer-checked:bg-indigo-600 transition-colors">
                                            <svg class="h-3 w-3 text-white peer-checked:opacity-100 opacity-0 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                        <!-- Checked Border styling -->
                                        <span class="pointer-events-none absolute -inset-0 rounded-2xl border-2 border-transparent peer-checked:border-indigo-600" aria-hidden="true"></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <!-- Navigation Controls -->
                        <div class="mt-10 flex items-center justify-between border-t border-gray-100 pt-6">
                            <!-- Prev Button -->
                            <button type="button" 
                                    @click="if(currentQ > 0) currentQ--" 
                                    class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                                    :class="{ 'opacity-50 cursor-not-allowed': currentQ === 0 }"
                                    :disabled="currentQ === 0">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                Sebelumnya
                            </button>

                            <!-- Next / Submit Button -->
                            <button type="button" 
                                    x-show="currentQ < total - 1"
                                    @click="currentQ++" 
                                    class="inline-flex items-center px-6 py-2 text-sm font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-colors">
                                Selanjutnya
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>

                            <button type="submit" 
                                    x-show="currentQ === total - 1"
                                    style="display: none;"
                                    class="inline-flex items-center px-6 py-2 text-sm font-bold rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-md transform hover:scale-105 transition-all">
                                Selesai & Lihat Hasil
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
