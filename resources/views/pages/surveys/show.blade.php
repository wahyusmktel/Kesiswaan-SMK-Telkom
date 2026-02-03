<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ $survey->title }}</h2>
    </x-slot>

    <div class="p-6">
        <div class="max-w-3xl mx-auto">
            <form action="{{ route('surveys.submit', $survey) }}" method="POST">
                @csrf

                <!-- Survey Header -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-10 mb-8 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-400 to-indigo-500"></div>
                    <h1 class="text-3xl font-extrabold text-slate-900 mb-4">{{ $survey->title }}</h1>
                    @if($survey->description)
                        <p class="text-slate-500 leading-relaxed">{{ $survey->description }}</p>
                    @endif
                    <div class="mt-6 flex items-center text-xs font-bold text-slate-400 uppercase tracking-widest">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Dibuat oleh: {{ $survey->creator->name }}
                    </div>
                </div>

                <!-- Questions -->
                <div class="space-y-6">
                    @foreach($survey->questions as $index => $question)
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                            <div class="flex items-start mb-6">
                                <span
                                    class="flex items-center justify-center w-6 h-6 bg-slate-100 text-slate-500 rounded-full font-bold text-xs mr-4 mt-1">{{ $index + 1 }}</span>
                                <h3 class="text-lg font-bold text-slate-800 leading-tight">{{ $question->question_text }}
                                </h3>
                            </div>

                            @if($question->type === 'multiple_choice')
                                <div class="space-y-3 ml-10">
                                    @foreach($question->options as $optIndex => $option)
                                        <label
                                            class="flex items-center p-4 bg-slate-50 border border-transparent rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-100 transition-all group">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" required
                                                class="w-5 h-5 text-blue-600 bg-white border-slate-300 focus:ring-blue-500 focus:ring-2 transition-all">
                                            <span
                                                class="ml-4 text-slate-700 font-medium group-hover:text-blue-700">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="ml-10">
                                    <textarea name="answers[{{ $question->id }}]" rows="4" required
                                        class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-slate-400"
                                        placeholder="Tuliskan jawaban Anda di sini..."></textarea>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Submission -->
                <div class="mt-10 flex justify-center">
                    <button type="submit"
                        class="px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-xl shadow-blue-200 transition-all duration-300 transform hover:-translate-y-1">
                        Kirim Jawaban
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>