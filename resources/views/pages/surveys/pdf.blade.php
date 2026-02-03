<!DOCTYPE html>
<html>

<head>
    <title>Hasil Survei: {{ $survey->title }}</title>
    <style>
        body {
            font-family: sans-serif;
            color: #334155;
            line-height: 1.5;
        }

        .header {
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
            margin: 0;
        }

        .meta {
            font-size: 12px;
            color: #64748b;
            margin-top: 5px;
        }

        .stats {
            margin-bottom: 30px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }

        .stats-table td {
            padding: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .stats-label {
            font-size: 10px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stats-value {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }

        .question-block {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }

        .question-header {
            background: #f1f5f9;
            padding: 10px 15px;
            border-left: 4px solid #3b82f6;
            margin-bottom: 15px;
        }

        .question-text {
            font-weight: bold;
            font-size: 16px;
            color: #1e293b;
        }

        .analysis-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .analysis-table th {
            text-align: left;
            font-size: 12px;
            color: #64748b;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .analysis-table td {
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        .essay-answer {
            padding: 15px;
            background: #fdfdfd;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="title">{{ $survey->title }}</h1>
        <div class="meta">
            Dibuat oleh: {{ $survey->creator->name }} &bull; Tanggal Laporan: {{ date('d F Y, H:i') }}
        </div>
    </div>

    <div class="stats">
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stats-label">Total Responden</div>
                    <div class="stats-value">{{ $survey->responses->count() }}</div>
                </td>
                <td>
                    <div class="stats-label">Jumlah Pertanyaan</div>
                    <div class="stats-value">{{ $survey->questions->count() }}</div>
                </td>
                <td>
                    <div class="stats-label">Status Survei</div>
                    <div class="stats-value">{{ $survey->is_active ? 'AKTIF' : 'DRAFT' }}</div>
                </td>
            </tr>
        </table>
    </div>

    @foreach($survey->questions as $index => $question)
        <div class="question-block">
            <div class="question-header">
                <div class="question-text">{{ $index + 1 }}. {{ $question->question_text }}</div>
            </div>

            @if($question->type === 'multiple_choice')
                <table class="analysis-table">
                    <thead>
                        <tr>
                            <th>Pilihan Jawaban</th>
                            <th style="text-align: right; width: 100px;">Jumlah</th>
                            <th style="text-align: right; width: 100px;">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $qData = $analysis[$question->id]; @endphp
                        @foreach($qData['labels'] as $labelIndex => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td style="text-align: right; font-weight: bold;">{{ $qData['values'][$labelIndex] }}</td>
                                <td style="text-align: right; color: #3b82f6; font-weight: bold;">
                                    {{ $survey->responses->count() > 0 ? round(($qData['values'][$labelIndex] / $survey->responses->count()) * 100) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="essay-responses">
                    @forelse($question->answers as $answer)
                        <div class="essay-answer">
                            <div style="font-weight: bold; margin-bottom: 5px; color: #64748b;">
                                {{ $answer->response->respondent->name }}:</div>
                            {{ $answer->answer_value }}
                        </div>
                    @empty
                        <div style="font-style: italic; color: #94a3b8;">Belum ada jawaban.</div>
                    @endforelse
                </div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        Dicetak otomatis oleh Sistem Informasi Sekolah (SISFO)
    </div>
</body>

</html>