<style>
    @page { margin: 14mm 14mm 15mm; }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        color: #252525;
        font-family: DejaVu Sans, sans-serif;
        font-size: 9.2pt;
        line-height: 1.25;
    }
    .school-mark {
        position: fixed;
        top: -9mm;
        left: 0;
        color: #777;
        font-size: 7.5pt;
    }
    .report-title { text-align: center; }
    .report-title h1 { margin: 0; font-size: 25pt; font-weight: 400; line-height: 1.15; }
    .report-title h2 { margin: 3px 0 0; font-size: 15pt; font-weight: 400; }
    .generated {
        margin-top: 3px;
        padding-top: 3px;
        border-top: 2px solid #ddd;
        text-align: center;
        font-size: 9pt;
    }
    .identity { margin: 22px 0 22px; }
    .identity h3, .recap-title {
        margin: 0 0 5px;
        font-size: 14pt;
        font-weight: 400;
        text-decoration: underline;
    }
    .identity table { width: auto; min-width: 58%; }
    .identity td { padding: 1px 0; border: 0; }
    .identity .label { width: 158px; }
    .identity .colon { width: 10px; }
    table.report-table { width: 100%; border-collapse: collapse; }
    .report-table thead { display: table-header-group; }
    .report-table tr { page-break-inside: avoid; }
    .report-table th,
    .report-table td { border: .8px solid #222; padding: 3px 4px; vertical-align: middle; }
    .report-table th { text-align: center; font-weight: 700; }
    .report-table .no { width: 26px; text-align: right; }
    .report-table .name { width: 30%; }
    .report-table .nip { width: 22%; }
    .report-table .nuptk { width: 21%; }
    .report-table .type { width: 20%; }
    .report-table .time { width: 54px; text-align: center; }
    .report-table .attendance-count { width: 116px; text-align: center; }
    .appendix { page-break-before: always; }
    .appendix-title-cell {
        padding: 9px 5px;
        text-align: center;
        font-size: 14pt;
        font-weight: 700;
    }
    .date-row td { padding: 4px; font-weight: 700; }
</style>
