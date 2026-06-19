<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Isi Penilaian</h2>
    </x-slot>

    <form method="POST" action="{{ route('penilaian.submit', [$instrument, $targetType, $target->id]) }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-xs font-black text-red-600 uppercase tracking-widest">{{ $instrument->type_label }}</p>
            <h3 class="text-xl font-extrabold text-gray-900">{{ $instrument->title }}</h3>
            <p class="text-sm text-gray-500 mt-1">Target penilaian: <span class="font-bold text-gray-700">{{ $target->nama_lengkap ?? $target->name }}</span></p>
        </div>

        @foreach($instrument->questions as $question)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <label class="block font-bold text-gray-900 mb-4">{{ $loop->iteration }}. {{ $question->question_text }}</label>

                @if($question->answer_type === 'text')
                    <textarea name="answers[{{ $question->id }}]" rows="4" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"></textarea>
                @elseif($question->answer_type === 'multiple_choice')
                    <div class="space-y-3">
                        @foreach($question->options ?? [] as $option)
                            <label class="flex items-center gap-3 text-sm text-gray-700">
                                <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span>{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($question->options ?? ['Ya', 'Tidak'] as $option)
                            <label class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 text-sm font-semibold text-gray-700 hover:bg-red-50">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" required class="border-gray-300 text-red-600 focus:ring-red-500">
                                <span>{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
                @error("answers.{$question->id}") <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
            </div>
        @endforeach

        <div class="flex justify-end gap-3">
            <a href="{{ route('penilaian.index') }}" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-bold text-gray-600">Batal</a>
            <button class="px-5 py-2 rounded-lg bg-red-600 text-white text-sm font-bold hover:bg-red-700">Kirim Penilaian</button>
        </div>
    </form>
</x-app-layout>
