<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Modul Ajar {{ $module->kode_modul }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 25.4mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #111111;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            line-height: 1.35;
        }

        .page-ribbon {
            position: fixed;
            top: -25.4mm;
            left: -25.4mm;
            width: 297mm;
            height: 17.3mm;
            z-index: -1;
        }

        .document-footer {
            position: fixed;
            right: 0;
            bottom: -18mm;
            left: 0;
            border-top: 0.6pt solid #7f1d1d;
            padding-top: 2.2mm;
            color: #4b5563;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .cover {
            height: 153mm;
            page-break-after: always;
            text-align: center;
        }

        .cover-logo {
            display: block;
            width: 106mm;
            height: auto;
            margin: 1mm auto 1.5mm;
        }

        .cover-title {
            margin: 0 0 4mm;
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
        }

        .cover-meta-wrap {
            width: 146mm;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 7mm;
            background: #c00000;
            color: #ffffff;
            padding: 4mm 7mm;
        }

        .cover-meta {
            width: 100%;
            border-collapse: collapse;
            color: #ffffff;
            font-size: 10pt;
            text-align: left;
        }

        .cover-meta td {
            border: 0;
            padding: 1.35mm 0;
            vertical-align: top;
        }

        .cover-meta .cover-label {
            width: 42mm;
            font-weight: bold;
        }

        .cover-meta .cover-separator {
            width: 5mm;
            text-align: center;
        }

        .page-block {
            width: 100%;
        }

        .document-heading {
            margin: 0 0 4mm;
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
        }

        table.module-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .module-table td,
        .module-table th {
            border: 0.7pt solid #111111;
            padding: 1mm 2.2mm;
            vertical-align: top;
        }

        .module-table tr {
            page-break-inside: avoid;
        }

        .metadata-table {
            margin-bottom: 3mm;
        }

        .metadata-table td {
            vertical-align: middle;
        }

        .meta-label,
        .subsection-label,
        .phase-title {
            background: #fae2d5;
            font-weight: bold;
        }

        .meta-value {
            font-weight: normal;
        }

        .section-label {
            width: 13.5%;
            background: #c00000;
            color: #ffffff;
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
            vertical-align: middle !important;
            word-break: normal;
        }

        .section-start {
            border-bottom-color: #c00000 !important;
        }

        .section-continuation {
            border-top-color: #c00000 !important;
            border-bottom-color: #c00000 !important;
        }

        .section-end {
            border-bottom-color: #111111 !important;
        }

        .subsection-label {
            width: 20.5%;
            vertical-align: middle !important;
        }

        .content-cell {
            width: 66%;
        }

        .meeting-heading {
            background: #404040;
            color: #ffffff;
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            vertical-align: middle !important;
            padding-top: 1.8mm !important;
            padding-bottom: 1.8mm !important;
        }

        .meeting-start {
            page-break-inside: avoid;
        }

        .meeting-table {
            margin-top: 0;
        }

        .meeting-table + .meeting-table {
            margin-top: -0.7pt;
        }

        .core-heading {
            background: #fae2d5;
            font-weight: bold;
            text-align: center;
            vertical-align: middle !important;
        }

        .phase-title {
            width: 43.25%;
            text-align: center;
            vertical-align: middle !important;
        }

        .phase-body {
            width: 43.25%;
            padding: 1.2mm 2.2mm !important;
        }

        .phase-body strong {
            display: block;
            margin-bottom: 0.8mm;
        }

        .phase-output {
            margin-top: 1.4mm;
            padding-top: 1.2mm;
            border-top: 0.4pt solid #9ca3af;
        }

        .principles {
            display: block;
            margin-top: 0.6mm;
            font-size: 8pt;
            font-style: italic;
            font-weight: normal;
        }

        .content-list {
            margin: 0;
            padding: 0;
        }

        .content-list-item {
            margin: 0 0 0.4mm;
            padding-left: 4mm;
            text-indent: -3.2mm;
        }

        .content-list-item:last-child {
            margin-bottom: 0;
        }

        .content-list-marker {
            display: inline-block;
            width: 1.15mm;
            height: 1.15mm;
            margin-right: 1.6mm;
            border-radius: 50%;
            background: #111111;
            vertical-align: 0.4mm;
        }

        .text-block {
            white-space: normal;
        }

        .empty-value {
            color: #6b7280;
        }

        .profile-table {
            width: 100%;
            margin-top: 1.5mm;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .profile-table td {
            width: 50%;
            border: 0;
            padding: 0.8mm 1.5mm 0.8mm 0;
            font-size: 8.7pt;
            vertical-align: top;
        }

        .check-mark {
            display: inline-block;
            width: 3.2mm;
            height: 3.2mm;
            margin-right: 1.2mm;
            border: 0.7pt solid #111111;
            font-size: 7pt;
            font-weight: bold;
            line-height: 3mm;
            text-align: center;
            vertical-align: middle;
        }

        .support-label {
            width: 34%;
            background: #ffffff;
            font-weight: bold;
        }

        .attachment-row {
            padding: 2.4mm !important;
        }

        .attachment-line {
            margin-bottom: 1.2mm;
        }

        .attachment-line:last-child {
            margin-bottom: 0;
        }

        .signature-table {
            width: 100%;
            margin-top: 5mm;
            border-collapse: collapse;
            page-break-inside: avoid;
            text-align: center;
        }

        .signature-table td {
            width: 36%;
            border: 0;
            padding: 0;
            vertical-align: top;
        }

        .signature-table .signature-spacer {
            width: 28%;
        }

        .signature-space {
            height: 24mm;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .page-break-before {
            page-break-before: always;
        }

        .mt-3mm {
            margin-top: 3mm;
        }
    </style>
</head>
<body>
    @if($ribbonDataUri)
        <img class="page-ribbon" src="{{ $ribbonDataUri }}" alt="">
    @endif

    <div class="document-footer">
        Modul Pembelajaran Mendalam TP. {{ $module->tahun_pelajaran }} -
        {{ mb_strtoupper($module->mata_pelajaran) }}
    </div>

    <section class="cover">
        @if($logoDataUri)
            <img class="cover-logo" src="{{ $logoDataUri }}" alt="{{ $module->instansi }}">
        @endif
        <h1 class="cover-title">MODUL PEMBELAJARAN MENDALAM</h1>
        <div class="cover-meta-wrap">
            <table class="cover-meta">
                <tr>
                    <td class="cover-label">Program Keahlian</td>
                    <td class="cover-separator">:</td>
                    <td>{{ $module->program_keahlian }}</td>
                </tr>
                <tr>
                    <td class="cover-label">Mata Pelajaran</td>
                    <td class="cover-separator">:</td>
                    <td>{{ $module->mata_pelajaran }}</td>
                </tr>
                <tr>
                    <td class="cover-label">Fase</td>
                    <td class="cover-separator">:</td>
                    <td>{{ $module->fase }}</td>
                </tr>
                <tr>
                    <td class="cover-label">Nama Penyusun</td>
                    <td class="cover-separator">:</td>
                    <td>{{ $module->nama_penyusun }}</td>
                </tr>
                <tr>
                    <td class="cover-label">Instansi</td>
                    <td class="cover-separator">:</td>
                    <td>{{ $module->instansi }}</td>
                </tr>
                <tr>
                    <td class="cover-label">Tahun Pelajaran</td>
                    <td class="cover-separator">:</td>
                    <td>{{ $module->tahun_pelajaran }}</td>
                </tr>
            </table>
        </div>
    </section>

    <section class="page-block">
        <h1 class="document-heading">MODUL PEMBELAJARAN MENDALAM</h1>

        <table class="module-table metadata-table">
            <colgroup>
                <col style="width:14.2%">
                <col style="width:23.9%">
                <col style="width:14.8%">
                <col style="width:13.8%">
                <col style="width:16.65%">
                <col style="width:16.65%">
            </colgroup>
            <tr>
                <td class="meta-label">Nama Modul</td>
                <td class="meta-value">{{ $module->nama_modul }}</td>
                <td class="meta-label">Jenjang/Kelas</td>
                <td class="meta-value">{{ $module->jenjang }}/{{ $module->kelas }}</td>
                <td class="meta-label">Kode Modul</td>
                <td class="meta-value">{{ $module->kode_modul }}</td>
            </tr>
            <tr>
                <td class="meta-label">Sekolah</td>
                <td class="meta-value">{{ $module->instansi }}</td>
                <td class="meta-label">Mata Pelajaran</td>
                <td class="meta-value" colspan="3">{{ $module->mata_pelajaran }}</td>
            </tr>
            <tr>
                <td class="meta-label">Alokasi Waktu</td>
                <td class="meta-value">{{ $module->alokasi_waktu }}</td>
                <td class="meta-label">Jumlah Murid</td>
                <td class="meta-value" colspan="3">{{ $module->jumlah_murid }}</td>
            </tr>
            <tr>
                <td class="meta-label">Fase</td>
                <td class="meta-value">{{ $module->fase }}</td>
                <td class="meta-label">Lingkup Materi</td>
                <td class="meta-value" colspan="3">{{ $module->lingkup_materi }}</td>
            </tr>
        </table>

        <table class="module-table">
            <colgroup>
                <col style="width:13.5%">
                <col style="width:20.5%">
                <col style="width:66%">
            </colgroup>
            <tr>
                <td class="section-label section-start">IDENTIFIKASI</td>
                <td class="subsection-label">01. Identifikasi Peserta Didik</td>
                <td class="content-cell">
                    @include('pdf.partials.teaching-module-list', ['items' => $content['identification']['students']])
                </td>
            </tr>
            <tr>
                <td class="section-label section-continuation"></td>
                <td class="subsection-label">02. Identifikasi Materi Pembelajaran</td>
                <td class="content-cell">
                    @include('pdf.partials.teaching-module-list', ['items' => $content['identification']['materials']])
                </td>
            </tr>
            <tr>
                <td class="section-label section-continuation section-end"></td>
                <td class="subsection-label">03. Dimensi Profil Lulusan</td>
                <td class="content-cell">
                    <div>Pilihlah dimensi profil lulusan yang akan dicapai dalam pembelajaran:</div>
                    @php
                        $profileRows = collect($content['identification']['graduate_profile'])->chunk(2);
                    @endphp
                    <table class="profile-table">
                        @foreach($profileRows as $profileRow)
                            <tr>
                                @foreach($profileRow as $dimension)
                                    <td>
                                        <span class="check-mark">{{ $dimension['selected'] ? 'X' : '' }}</span>
                                        {{ $dimension['label'] }}
                                        @if($dimension['selected'] && trim($dimension['note']) !== '')
                                            <br><span style="padding-left:5mm; color:#4b5563;">{{ $dimension['note'] }}</span>
                                        @endif
                                    </td>
                                @endforeach
                                @if($profileRow->count() === 1)<td></td>@endif
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>

            @php
                $designRows = [
                    ['Capaian Pembelajaran', 'learning_outcomes'],
                    ['Tujuan Pembelajaran', 'learning_objectives'],
                    ['Topik Pembelajaran', 'learning_topics'],
                    ['Praktik Pedagogi', 'pedagogical_practices'],
                    ['Mitra Pembelajaran', 'learning_partners'],
                    ['Lingkungan Belajar', 'learning_environment'],
                    ['Pemanfaatan Digital', 'digital_use'],
                ];
            @endphp
            @foreach($designRows as $designIndex => [$label, $key])
                <tr>
                    <td class="section-label {{ $designIndex === 0 ? 'section-start' : 'section-continuation' }} {{ $designIndex === count($designRows) - 1 ? 'section-end' : '' }}">
                        {{ $designIndex === 0 ? 'DESAIN PEMBELAJARAN' : '' }}
                    </td>
                    <td class="subsection-label">{{ str_pad($designIndex + 1, 2, '0', STR_PAD_LEFT) }}. {{ $label }}</td>
                    <td class="content-cell">
                        @include('pdf.partials.teaching-module-list', ['items' => $content['design'][$key]])
                    </td>
                </tr>
            @endforeach
        </table>
    </section>

    @foreach($content['experiences'] as $meetingIndex => $meeting)
        <div class="meeting-start mt-3mm">
            <table class="module-table meeting-table">
                <tr>
                    <td class="meeting-heading">
                        Langkah-Langkah Pembelajaran Pertemuan {{ $meetingIndex + 1 }} ({{ $meeting['allocation'] }})
                        @if(trim($meeting['title']) !== '')<br>{{ $meeting['title'] }}@endif
                    </td>
                </tr>
            </table>
            <table class="module-table meeting-table">
                <colgroup>
                    <col style="width:13.5%">
                    <col style="width:20.5%">
                    <col style="width:66%">
                </colgroup>
                <tr>
                    <td class="section-label" width="13.5%">PENGALAMAN<br>BELAJAR</td>
                    <td class="subsection-label" width="20.5%">Kegiatan Awal Pembelajaran<br><span class="principles">(Berkesadaran, bermakna, menggembirakan)</span></td>
                    <td class="content-cell" width="66%">
                        @include('pdf.partials.teaching-module-list', ['items' => $meeting['opening']])
                    </td>
                </tr>
            </table>
        </div>

        <table class="module-table meeting-table">
            <colgroup>
                <col style="width:13.5%">
                <col style="width:43.25%">
                <col style="width:43.25%">
            </colgroup>
            <tr>
                <td class="section-label section-continuation" width="13.5%"></td>
                <td colspan="2" class="core-heading" width="86.5%">Kegiatan Inti<br><span class="principles">(Berkesadaran, bermakna, menggembirakan)</span></td>
            </tr>

            @foreach(array_chunk($meeting['core_phases'], 2) as $phasePair)
                <tr>
                    <td class="section-label section-continuation" width="13.5%"></td>
                    @foreach($phasePair as $phase)
                        <td class="phase-title" width="43.25%">
                            {{ $phase['title'] }}
                            @if(trim($phase['principles']) !== '')
                                <span class="principles">({{ $phase['principles'] }})</span>
                            @endif
                        </td>
                    @endforeach
                    @if(count($phasePair) === 1)<td class="phase-title" width="43.25%"></td>@endif
                </tr>
                <tr>
                    <td class="section-label section-continuation" width="13.5%"></td>
                    @foreach($phasePair as $phase)
                        <td class="phase-body" width="43.25%">
                            <strong>Aktivitas Guru:</strong>
                            @include('pdf.partials.teaching-module-list', ['items' => $phase['teacher_activities']])
                            <div style="height:1.5mm"></div>
                            <strong>Aktivitas Peserta Didik:</strong>
                            @include('pdf.partials.teaching-module-list', ['items' => $phase['student_activities']])
                            <div class="phase-output">
                                <strong>Output:</strong>
                                @include('pdf.partials.teaching-module-list', ['items' => $phase['outputs']])
                            </div>
                        </td>
                    @endforeach
                    @if(count($phasePair) === 1)<td class="phase-body" width="43.25%"></td>@endif
                </tr>
            @endforeach
        </table>

        <table class="module-table meeting-table">
            <colgroup>
                <col style="width:13.5%">
                <col style="width:20.5%">
                <col style="width:66%">
            </colgroup>
            <tr>
                <td class="section-label section-continuation" width="13.5%"></td>
                <td class="subsection-label" width="20.5%">Kegiatan Penutup<br><span class="principles">(Berkesadaran, bermakna, menggembirakan)</span></td>
                <td class="content-cell" width="66%">
                    @include('pdf.partials.teaching-module-list', ['items' => $meeting['closing']])
                </td>
            </tr>
        </table>
    @endforeach

    <table class="module-table mt-3mm">
        <colgroup>
            <col style="width:13.5%">
            <col style="width:20.5%">
            <col style="width:66%">
        </colgroup>
        @php
            $assessmentRows = [
                ['Asesmen Awal Pembelajaran', 'initial'],
                ['Asesmen pada Proses Pembelajaran', 'process'],
                ['Asesmen Akhir Pembelajaran', 'final'],
                ['Kriteria Ketercapaian Tujuan Pembelajaran', 'criteria'],
            ];
        @endphp
        @foreach($assessmentRows as $assessmentIndex => [$label, $key])
            <tr>
                <td class="section-label {{ $assessmentIndex === 0 ? 'section-start' : 'section-continuation' }} {{ $assessmentIndex === count($assessmentRows) - 1 ? 'section-end' : '' }}">
                    {{ $assessmentIndex === 0 ? 'ASESMEN PEMBELAJARAN' : '' }}
                </td>
                <td class="subsection-label">{{ $label }}</td>
                <td class="content-cell">
                    @include('pdf.partials.teaching-module-list', ['items' => $content['assessment'][$key]])
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2" class="support-label">Pertanyaan Pemantik</td>
            <td class="content-cell">
                @include('pdf.partials.teaching-module-list', ['items' => $content['supporting']['trigger_questions']])
            </td>
        </tr>
        <tr>
            <td colspan="2" class="support-label">Diferensiasi Pembelajaran</td>
            <td class="content-cell">
                @include('pdf.partials.teaching-module-list', ['items' => $content['supporting']['differentiation']])
            </td>
        </tr>
        <tr>
            <td colspan="2" class="support-label">Pengayaan dan Remedial</td>
            <td class="content-cell">
                <strong>Pengayaan:</strong>
                @include('pdf.partials.teaching-module-list', ['items' => $content['supporting']['enrichment']])
                <div style="height:1.5mm"></div>
                <strong>Remedial:</strong>
                @include('pdf.partials.teaching-module-list', ['items' => $content['supporting']['remedial']])
            </td>
        </tr>
        <tr>
            <td colspan="3" class="attachment-row">
                <div class="attachment-line"><strong>Lampiran - Bahan Ajar:</strong>
                    @include('pdf.partials.teaching-module-list', ['items' => $content['attachments']['teaching_materials']])
                </div>
                <div class="attachment-line"><strong>Lembar Kerja:</strong>
                    @include('pdf.partials.teaching-module-list', ['items' => $content['attachments']['worksheets']])
                </div>
                <div class="attachment-line"><strong>Asesmen:</strong>
                    @include('pdf.partials.teaching-module-list', ['items' => $content['attachments']['assessments']])
                </div>
            </td>
        </tr>
    </table>

    <table class="signature-table">
        <tr>
            <td>Validator,</td>
            <td class="signature-spacer"></td>
            <td>{{ $content['approval']['location'] }}, {{ $approvalDate?->translatedFormat('d F Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td>{{ $content['approval']['validator_title'] }}</td>
            <td class="signature-spacer"></td>
            <td>{{ $content['approval']['teacher_title'] }}</td>
        </tr>
        <tr>
            <td><div class="signature-space"></div></td>
            <td class="signature-spacer"></td>
            <td><div class="signature-space"></div></td>
        </tr>
        <tr>
            <td>
                <span class="signature-name">{{ $content['approval']['validator_name'] ?: '-' }}</span><br>
                NIP. {{ $content['approval']['validator_nip'] ?: '-' }}
            </td>
            <td class="signature-spacer"></td>
            <td>
                <span class="signature-name">{{ $content['approval']['teacher_name'] ?: $module->nama_penyusun }}</span><br>
                NIP. {{ $content['approval']['teacher_nip'] ?: '-' }}
            </td>
        </tr>
    </table>
</body>
</html>
