# Artifact Contract: Modul Ajar 1.1

## Reference identity

- Immutable source: `public/12. Modul Ajar 1.1.docx`
- Working copy: `tmp/modul-ajar-reference/reference-copy.docx`
- SHA-256: `700E7D132CFB696BAD714A00D8948D044EBFBD8AB1DF7B19BB7CBC198AE67113`
- Source size: 93,994 bytes
- Microsoft Word reports 6 rendered pages.

## Page geometry

- One section, A4 landscape (`11.69 x 8.27 in`).
- Margins: 1 inch on every side.
- Primary typeface: Arial; most body content is 10 pt.
- The title is centered, bold, and 14 pt.
- The document uses direct formatting rather than named heading styles.

## Visual language

- Primary section color: dark red `#C00000`, with white uppercase text.
- Secondary cell color: pale peach `#FAE2D5`.
- Meeting divider color: dark gray `#404040`, with white centered text.
- Tables use thin black borders and compact cell padding.
- The top header contains a full-width red ribbon image (`word/media/image2.png`).
- The cover contains the SMK Telkom Lampung logo (`word/media/image1.png`).
- The footer text is: `MODUL PEMBELAJARAN MENDALAM TP. {tahun} - {mata pelajaran}`.

## Content flow and slot map

1. Cover
   - School logo.
   - Title `MODUL PEMBELAJARAN MENDALAM`.
   - Program Keahlian, Mata Pelajaran, Fase, Nama Penyusun, Instansi, and Tahun Pelajaran.
2. Module metadata table
   - Nama Modul, Jenjang/Kelas, Kode Modul.
   - Sekolah, Mata Pelajaran.
   - Alokasi Waktu, Jumlah Murid.
   - Fase, Lingkup Materi.
3. Identification table
   - Identifikasi Peserta Didik.
   - Identifikasi Materi Pembelajaran.
   - Dimensi Profil Lulusan.
4. Learning design table
   - Capaian Pembelajaran.
   - Tujuan Pembelajaran.
   - Topik Pembelajaran.
   - Praktik Pedagogi.
   - Mitra Pembelajaran.
   - Lingkungan Belajar.
   - Pemanfaatan Digital.
5. Learning experience, repeated per meeting
   - Meeting heading and time allocation.
   - Kegiatan Awal Pembelajaran.
   - Kegiatan Inti with repeatable phases.
   - Each core phase may contain Aktivitas Guru, Aktivitas Peserta Didik, and Output.
   - Kegiatan Penutup.
6. Assessment and supporting content
   - Asesmen Awal, Proses, Akhir, and Kriteria Ketercapaian.
   - Pertanyaan Pemantik.
   - Diferensiasi Pembelajaran.
   - Pengayaan and Remedial.
   - Lampiran: Bahan Ajar, Lembar Kerja, and Asesmen.
7. Approval block
   - Validator/Waka Kurikulum on the left.
   - Guru Mata Pelajaran and date/location on the right.

## Data and rendering rules

- Metadata is stored in dedicated columns for filtering and stable snapshots.
- Long-form module content is stored as structured JSON with versioned defaults.
- Every repeatable list must preserve user order and support add/remove interactions.
- Empty optional rows are omitted from the PDF; required structural headings remain.
- User-provided text is escaped by Blade and rendered with preserved line breaks.
- The output PDF uses A4 landscape and the same table hierarchy, colors, and relative column widths as the reference.
- Dynamic content may increase the page count; rows should avoid splitting where practical without clipping content.

## Fidelity gates

- Reference package, relationships, header/footer geometry, images, tables, merged cells, fills, and row ordering were audited from OOXML.
- Word export to PDF stalled in the local Office installation, so page-image comparison of the source is unavailable.
- Final verification must render the application PDF to PNG and inspect all pages for clipping, table continuity, color hierarchy, and signature placement.
- The original reference file must remain byte-for-byte unchanged.
