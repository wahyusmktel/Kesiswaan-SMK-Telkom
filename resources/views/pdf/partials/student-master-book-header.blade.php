<div class="document-header">
    <table>
        <tr>
            <td>
                <div class="school-name">{{ mb_strtoupper($schoolName) }}</div>
                <div class="meta">BUKU INDUK PESERTA DIDIK · NPSN {{ $npsn ?: '-' }}</div>
            </td>
            <td style="text-align:right;">
                <strong>{{ $student->nama_lengkap }}</strong>
                <div class="meta">NIS {{ $student->nis }} · NISN {{ $student->dapodik?->nisn ?: '-' }}</div>
            </td>
        </tr>
    </table>
</div>
