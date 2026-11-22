class UkkExamItem {
  const UkkExamItem({
    required this.id,
    required this.namaUjian,
    this.nomorPaket,
    this.judulPaket,
    this.tahunPelajaran,
    this.semester,
    required this.isBilingual,
    required this.status,
    required this.peranPenguji,
    required this.totalSiswa,
    required this.sudahDinilai,
    required this.persentaseSelesai,
    required this.rombelNames,
  });

  final int id;
  final String namaUjian;
  final String? nomorPaket;
  final String? judulPaket;
  final String? tahunPelajaran;
  final String? semester;
  final bool isBilingual;
  final String status;
  final String peranPenguji;
  final int totalSiswa;
  final int sudahDinilai;
  final double persentaseSelesai;
  final List<String> rombelNames;

  factory UkkExamItem.fromJson(Map<String, dynamic> json) {
    final rombelsRaw = json['rombel_names'];
    final rombelNames = rombelsRaw is List
        ? rombelsRaw.map((e) => e.toString()).toList()
        : <String>[];

    return UkkExamItem(
      id: (json['id'] as num).toInt(),
      namaUjian: json['nama_ujian']?.toString() ?? '-',
      nomorPaket: json['nomor_paket']?.toString(),
      judulPaket: json['judul_paket']?.toString(),
      tahunPelajaran: json['tahun_pelajaran']?.toString(),
      semester: json['semester']?.toString(),
      isBilingual: json['is_bilingual'] == true,
      status: json['status']?.toString() ?? 'Aktif',
      peranPenguji: json['peran_penguji']?.toString() ?? 'semua',
      totalSiswa: (json['total_siswa'] as num?)?.toInt() ?? 0,
      sudahDinilai: (json['sudah_dinilai'] as num?)?.toInt() ?? 0,
      persentaseSelesai: (json['persentase_selesai'] as num?)?.toDouble() ?? 0.0,
      rombelNames: rombelNames,
    );
  }
}

class UkkRombelItem {
  const UkkRombelItem({
    required this.id,
    required this.namaKelas,
  });

  final int id;
  final String namaKelas;

  factory UkkRombelItem.fromJson(Map<String, dynamic> json) {
    return UkkRombelItem(
      id: (json['id'] as num).toInt(),
      namaKelas: json['nama_kelas']?.toString() ?? '-',
    );
  }
}

class UkkStudentItem {
  const UkkStudentItem({
    required this.id,
    required this.namaLengkap,
    required this.nis,
    this.nisn,
    this.nomorUrut,
    this.rombelId,
    this.rombelNama,
    this.foto,
    required this.status,
    required this.isCompleted,
    required this.gradedPengetahuan,
    required this.totalPengetahuan,
    required this.gradedKeterampilan,
    required this.totalKeterampilan,
    required this.hasSikap,
    required this.hasBilingual,
  });

  final int id;
  final String namaLengkap;
  final String nis;
  final String? nisn;
  final int? nomorUrut;
  final int? rombelId;
  final String? rombelNama;
  final String? foto;
  final String status; // 'selesai' | 'sebagian' | 'belum'
  final bool isCompleted;
  final int gradedPengetahuan;
  final int totalPengetahuan;
  final int gradedKeterampilan;
  final int totalKeterampilan;
  final bool hasSikap;
  final bool hasBilingual;

  factory UkkStudentItem.fromJson(Map<String, dynamic> json) {
    return UkkStudentItem(
      id: (json['id'] as num).toInt(),
      namaLengkap: json['nama_lengkap']?.toString() ?? '-',
      nis: json['nis']?.toString() ?? '-',
      nisn: json['nisn']?.toString(),
      nomorUrut: (json['nomor_urut'] as num?)?.toInt(),
      rombelId: (json['rombel_id'] as num?)?.toInt(),
      rombelNama: json['rombel_nama']?.toString(),
      foto: json['foto']?.toString(),
      status: json['status']?.toString() ?? 'belum',
      isCompleted: json['is_completed'] == true,
      gradedPengetahuan: (json['graded_pengetahuan'] as num?)?.toInt() ?? 0,
      totalPengetahuan: (json['total_pengetahuan'] as num?)?.toInt() ?? 0,
      gradedKeterampilan: (json['graded_keterampilan'] as num?)?.toInt() ?? 0,
      totalKeterampilan: (json['total_keterampilan'] as num?)?.toInt() ?? 0,
      hasSikap: json['has_sikap'] == true,
      hasBilingual: json['has_bilingual'] == true,
    );
  }
}

class UkkStudentDetail {
  const UkkStudentDetail({
    required this.id,
    required this.namaLengkap,
    required this.nis,
    this.nisn,
    this.rombelNama,
    this.foto,
  });

  final int id;
  final String namaLengkap;
  final String nis;
  final String? nisn;
  final String? rombelNama;
  final String? foto;

  factory UkkStudentDetail.fromJson(Map<String, dynamic> json) {
    return UkkStudentDetail(
      id: (json['id'] as num).toInt(),
      namaLengkap: json['nama_lengkap']?.toString() ?? '-',
      nis: json['nis']?.toString() ?? '-',
      nisn: json['nisn']?.toString(),
      rombelNama: json['rombel_nama']?.toString(),
      foto: json['foto']?.toString(),
    );
  }
}

class UkkExamDetail {
  const UkkExamDetail({
    required this.id,
    required this.namaUjian,
    this.nomorPaket,
    this.judulPaket,
    required this.isBilingual,
  });

  final int id;
  final String namaUjian;
  final String? nomorPaket;
  final String? judulPaket;
  final bool isBilingual;

  factory UkkExamDetail.fromJson(Map<String, dynamic> json) {
    return UkkExamDetail(
      id: (json['id'] as num).toInt(),
      namaUjian: json['nama_ujian']?.toString() ?? '-',
      nomorPaket: json['nomor_paket']?.toString(),
      judulPaket: json['judul_paket']?.toString(),
      isBilingual: json['is_bilingual'] == true,
    );
  }
}

class UkkPermissions {
  const UkkPermissions({
    required this.peranPenguji,
    required this.canGradePengetahuan,
    required this.canGradeKeterampilan,
    required this.canGradeSikap,
    required this.canGradeBilingual,
  });

  final String peranPenguji;
  final bool canGradePengetahuan;
  final bool canGradeKeterampilan;
  final bool canGradeSikap;
  final bool canGradeBilingual;

  factory UkkPermissions.fromJson(Map<String, dynamic> json) {
    return UkkPermissions(
      peranPenguji: json['peran_penguji']?.toString() ?? 'semua',
      canGradePengetahuan: json['can_grade_pengetahuan'] == true,
      canGradeKeterampilan: json['can_grade_keterampilan'] == true,
      canGradeSikap: json['can_grade_sikap'] == true,
      canGradeBilingual: json['can_grade_bilingual'] == true,
    );
  }
}

class UkkScoresSummary {
  const UkkScoresSummary({
    required this.skorPengetahuan,
    required this.skorPengetahuanKonten,
    this.skorBilingualKecakapan,
    required this.skorKeterampilan,
  });

  final double skorPengetahuan;
  final double skorPengetahuanKonten;
  final double? skorBilingualKecakapan;
  final double skorKeterampilan;

  factory UkkScoresSummary.fromJson(Map<String, dynamic> json) {
    return UkkScoresSummary(
      skorPengetahuan: (json['skor_pengetahuan'] as num?)?.toDouble() ?? 0.0,
      skorPengetahuanKonten: (json['skor_pengetahuan_konten'] as num?)?.toDouble() ?? 0.0,
      skorBilingualKecakapan: (json['skor_bilingual_kecakapan'] as num?)?.toDouble(),
      skorKeterampilan: (json['skor_keterampilan'] as num?)?.toDouble() ?? 0.0,
    );
  }
}

class UkkSoalPengetahuan {
  UkkSoalPengetahuan({
    required this.id,
    this.nomorSoal,
    required this.pertanyaan,
    this.kunciJawaban,
    this.jenisSoal,
    this.nilai,
    this.catatan,
  });

  final int id;
  final String? nomorSoal;
  final String pertanyaan;
  final String? kunciJawaban;
  final String? jenisSoal;
  int? nilai; // 1 = Benar, 0 = Salah, null = Belum
  String? catatan;

  bool get isBilingual => jenisSoal == 'bilingual';

  factory UkkSoalPengetahuan.fromJson(Map<String, dynamic> json) {
    return UkkSoalPengetahuan(
      id: (json['id'] as num).toInt(),
      nomorSoal: json['nomor_soal']?.toString(),
      pertanyaan: json['pertanyaan']?.toString() ?? '',
      kunciJawaban: json['kunci_jawaban']?.toString(),
      jenisSoal: json['jenis_soal']?.toString(),
      nilai: (json['nilai'] as num?)?.toInt(),
      catatan: json['catatan']?.toString(),
    );
  }
}

class UkkIndikatorKeterampilan {
  UkkIndikatorKeterampilan({
    required this.id,
    this.nomorIndikator,
    required this.namaIndikator,
    required this.bobot,
    this.nilai,
  });

  final int id;
  final String? nomorIndikator;
  final String namaIndikator;
  final int bobot;
  int? nilai; // 0, 1, 2, 3

  factory UkkIndikatorKeterampilan.fromJson(Map<String, dynamic> json) {
    return UkkIndikatorKeterampilan(
      id: (json['id'] as num).toInt(),
      nomorIndikator: json['nomor_indikator']?.toString(),
      namaIndikator: json['nama_indikator']?.toString() ?? '',
      bobot: (json['bobot'] as num?)?.toInt() ?? 1,
      nilai: (json['nilai'] as num?)?.toInt(),
    );
  }
}

class UkkKategoriKeterampilan {
  const UkkKategoriKeterampilan({
    required this.id,
    required this.namaKategori,
    required this.indikators,
  });

  final int id;
  final String namaKategori;
  final List<UkkIndikatorKeterampilan> indikators;

  factory UkkKategoriKeterampilan.fromJson(Map<String, dynamic> json) {
    final listRaw = json['indikators'] as List? ?? [];
    return UkkKategoriKeterampilan(
      id: (json['id'] as num).toInt(),
      namaKategori: json['nama_kategori']?.toString() ?? 'Kategori',
      indikators: listRaw
          .map((e) => UkkIndikatorKeterampilan.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}

class UkkInstrumen {
  const UkkInstrumen({
    required this.id,
    required this.namaInstrumen,
    required this.bobotPengetahuan,
    required this.bobotKeterampilan,
    required this.soalPengetahuan,
    required this.kategoriKeterampilan,
  });

  final int id;
  final String namaInstrumen;
  final int bobotPengetahuan;
  final int bobotKeterampilan;
  final List<UkkSoalPengetahuan> soalPengetahuan;
  final List<UkkKategoriKeterampilan> kategoriKeterampilan;

  factory UkkInstrumen.fromJson(Map<String, dynamic> json) {
    final soalsRaw = json['soal_pengetahuan'] as List? ?? [];
    final katsRaw = json['kategori_keterampilan'] as List? ?? [];

    return UkkInstrumen(
      id: (json['id'] as num).toInt(),
      namaInstrumen: json['nama_instrumen']?.toString() ?? 'Instrumen',
      bobotPengetahuan: (json['bobot_pengetahuan'] as num?)?.toInt() ?? 30,
      bobotKeterampilan: (json['bobot_keterampilan'] as num?)?.toInt() ?? 70,
      soalPengetahuan: soalsRaw
          .map((e) => UkkSoalPengetahuan.fromJson(e as Map<String, dynamic>))
          .toList(),
      kategoriKeterampilan: katsRaw
          .map((e) => UkkKategoriKeterampilan.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}

class UkkNilaiSikapData {
  UkkNilaiSikapData({
    this.kerapian,
    this.ketelitian,
    this.kedisiplinan,
    this.tanggungJawab,
    this.inisiatif,
    this.kesimpulan,
    this.catatan,
  });

  String? kerapian; // 'kurang', 'cukup', 'baik', 'sangat_baik'
  String? ketelitian;
  String? kedisiplinan;
  String? tanggungJawab;
  String? inisiatif;
  String? kesimpulan;
  String? catatan;

  bool get isFilled =>
      kerapian != null ||
      ketelitian != null ||
      kedisiplinan != null ||
      tanggungJawab != null ||
      inisiatif != null;

  factory UkkNilaiSikapData.fromJson(Map<String, dynamic> json) {
    return UkkNilaiSikapData(
      kerapian: json['kerapian']?.toString(),
      ketelitian: json['ketelitian']?.toString(),
      kedisiplinan: json['kedisiplinan']?.toString(),
      tanggungJawab: json['tanggung_jawab']?.toString(),
      inisiatif: json['inisiatif']?.toString(),
      kesimpulan: json['kesimpulan']?.toString(),
      catatan: json['catatan']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'kerapian': kerapian,
      'ketelitian': ketelitian,
      'kedisiplinan': kedisiplinan,
      'tanggung_jawab': tanggungJawab,
      'inisiatif': inisiatif,
      'kesimpulan': kesimpulan,
      'catatan': catatan,
    };
  }
}

class UkkNilaiBilingualData {
  UkkNilaiBilingualData({
    this.fluency,
    this.pronunciation,
    this.vocabulary,
    this.comprehension,
    this.kesimpulan,
    this.catatan,
  });

  String? fluency; // 'poor', 'fair', 'good', 'excellent'
  String? pronunciation;
  String? vocabulary;
  String? comprehension;
  String? kesimpulan;
  String? catatan;

  bool get isFilled =>
      fluency != null ||
      pronunciation != null ||
      vocabulary != null ||
      comprehension != null;

  int? _rubricVal(String? val) {
    switch (val) {
      case 'poor': return 1;
      case 'fair': return 2;
      case 'good': return 3;
      case 'excellent': return 4;
      default: return null;
    }
  }

  double? get averageScore {
    final vals = [
      _rubricVal(fluency),
      _rubricVal(pronunciation),
      _rubricVal(vocabulary),
      _rubricVal(comprehension),
    ].whereType<int>().toList();

    if (vals.isEmpty) return null;
    return vals.reduce((a, b) => a + b) / vals.length;
  }

  factory UkkNilaiBilingualData.fromJson(Map<String, dynamic> json) {
    return UkkNilaiBilingualData(
      fluency: json['fluency']?.toString(),
      pronunciation: json['pronunciation']?.toString(),
      vocabulary: json['vocabulary']?.toString(),
      comprehension: json['comprehension']?.toString(),
      kesimpulan: json['kesimpulan']?.toString(),
      catatan: json['catatan']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'fluency': fluency,
      'pronunciation': pronunciation,
      'vocabulary': vocabulary,
      'comprehension': comprehension,
      'kesimpulan': kesimpulan,
      'catatan': catatan,
    };
  }
}

class UkkAssessmentDetailResponse {
  const UkkAssessmentDetailResponse({
    required this.siswa,
    required this.ujian,
    required this.permissions,
    required this.scoresSummary,
    required this.instrumens,
    this.sikap,
    this.bilingual,
  });

  final UkkStudentDetail siswa;
  final UkkExamDetail ujian;
  final UkkPermissions permissions;
  final UkkScoresSummary scoresSummary;
  final List<UkkInstrumen> instrumens;
  final UkkNilaiSikapData? sikap;
  final UkkNilaiBilingualData? bilingual;

  factory UkkAssessmentDetailResponse.fromJson(Map<String, dynamic> json) {
    final instrumensRaw = json['instrumens'] as List? ?? [];
    return UkkAssessmentDetailResponse(
      siswa: UkkStudentDetail.fromJson(json['siswa'] as Map<String, dynamic>),
      ujian: UkkExamDetail.fromJson(json['ujian'] as Map<String, dynamic>),
      permissions: UkkPermissions.fromJson(json['permissions'] as Map<String, dynamic>),
      scoresSummary: UkkScoresSummary.fromJson(json['scores_summary'] as Map<String, dynamic>),
      instrumens: instrumensRaw
          .map((e) => UkkInstrumen.fromJson(e as Map<String, dynamic>))
          .toList(),
      sikap: json['sikap'] != null
          ? UkkNilaiSikapData.fromJson(json['sikap'] as Map<String, dynamic>)
          : null,
      bilingual: json['bilingual'] != null
          ? UkkNilaiBilingualData.fromJson(json['bilingual'] as Map<String, dynamic>)
          : null,
    );
  }
}
