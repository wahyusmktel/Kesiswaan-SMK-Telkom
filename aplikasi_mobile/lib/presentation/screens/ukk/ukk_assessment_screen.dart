import 'package:flutter/material.dart';

import '../../../core/theme/app_theme.dart';
import '../../../data/models/ukk/ukk_models.dart';
import '../../../data/services/ukk_service.dart';

class UkkAssessmentScreen extends StatefulWidget {
  const UkkAssessmentScreen({
    required this.exam,
    required this.student,
    super.key,
  });

  final UkkExamItem exam;
  final UkkStudentItem student;

  @override
  State<UkkAssessmentScreen> createState() => _UkkAssessmentScreenState();
}

class _UkkAssessmentScreenState extends State<UkkAssessmentScreen>
    with SingleTickerProviderStateMixin {
  final _ukkService = UkkService();

  bool _loading = true;
  String? _error;
  UkkAssessmentDetailResponse? _detail;

  // Local state for assessments
  final Map<int, int?> _pengetahuanValues = {};
  final Map<int, TextEditingController> _pengetahuanNotes = {};
  final Map<int, int?> _keterampilanValues = {};
  late UkkNilaiSikapData _sikapData;
  late UkkNilaiBilingualData _bilingualData;

  final TextEditingController _sikapCatatanController = TextEditingController();
  final TextEditingController _bilingualCatatanController = TextEditingController();

  TabController? _tabController;
  final List<String> _tabTitles = [];

  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _loadAssessment();
  }

  @override
  void dispose() {
    _tabController?.dispose();
    for (final c in _pengetahuanNotes.values) {
      c.dispose();
    }
    _sikapCatatanController.dispose();
    _bilingualCatatanController.dispose();
    super.dispose();
  }

  Future<void> _loadAssessment() async {
    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      final res = await _ukkService.getStudentAssessment(
        widget.exam.id,
        widget.student.id,
      );

      if (!mounted) return;

      // Populate local maps
      for (final ins in res.instrumens) {
        for (final soal in ins.soalPengetahuan) {
          _pengetahuanValues[soal.id] = soal.nilai;
          _pengetahuanNotes[soal.id] = TextEditingController(text: soal.catatan ?? '');
        }
        for (final kat in ins.kategoriKeterampilan) {
          for (final ind in kat.indikators) {
            _keterampilanValues[ind.id] = ind.nilai;
          }
        }
      }

      _sikapData = res.sikap ?? UkkNilaiSikapData();
      _sikapCatatanController.text = _sikapData.catatan ?? '';

      _bilingualData = res.bilingual ?? UkkNilaiBilingualData();
      _bilingualCatatanController.text = _bilingualData.catatan ?? '';

      // Determine tabs
      _tabTitles.clear();
      final hasSoal = res.instrumens.any((i) => i.soalPengetahuan.isNotEmpty);
      final hasKat = res.instrumens.any((i) => i.kategoriKeterampilan.isNotEmpty);

      if (res.permissions.canGradePengetahuan && hasSoal) {
        _tabTitles.add('Pengetahuan');
      }
      if (res.permissions.canGradeBilingual) {
        _tabTitles.add('Bilingual Rubrik');
      }
      if (res.permissions.canGradeKeterampilan && hasKat) {
        _tabTitles.add('Keterampilan');
      }
      if (res.permissions.canGradeSikap) {
        _tabTitles.add('Sikap Kerja');
      }

      _tabController?.dispose();
      _tabController = TabController(length: _tabTitles.length, vsync: this);

      setState(() {
        _detail = res;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e.toString();
        _loading = false;
      });
    }
  }

  // --- Real-time score calculations ---
  double get _skorPKonten {
    int total = 0;
    int benar = 0;
    for (final ins in _detail?.instrumens ?? <UkkInstrumen>[]) {
      for (final s in ins.soalPengetahuan) {
        total++;
        if (_pengetahuanValues[s.id] == 1) {
          benar++;
        }
      }
    }
    return total > 0 ? (benar / total) * 100 : 0.0;
  }

  double? get _skorPBilingualSpeaking {
    final avg = _bilingualData.averageScore;
    if (avg == null) return null;
    return (avg / 4.0) * 100;
  }

  double get _skorPFinal {
    final konten = _skorPKonten;
    if (widget.exam.isBilingual && _skorPBilingualSpeaking != null) {
      return (konten * 0.5) + (_skorPBilingualSpeaking! * 0.5);
    }
    return konten;
  }

  double get _skorKFinal {
    int totalMax = 0;
    int totalVal = 0;
    for (final ins in _detail?.instrumens ?? <UkkInstrumen>[]) {
      for (final kat in ins.kategoriKeterampilan) {
        for (final ind in kat.indikators) {
          totalMax += 3;
          totalVal += (_keterampilanValues[ind.id] ?? 0);
        }
      }
    }
    return totalMax > 0 ? (totalVal / totalMax) * 100 : 0.0;
  }

  double get _nilaiAkhirFinal {
    final instrumens = _detail?.instrumens ?? <UkkInstrumen>[];
    if (instrumens.isEmpty) return 0.0;

    double totalAkhir = 0.0;
    for (final ins in instrumens) {
      final bp = ins.bobotPengetahuan / 100.0;
      final bk = ins.bobotKeterampilan / 100.0;
      final na = (_skorPFinal * bp) + (_skorKFinal * bk);
      totalAkhir += na;
    }
    return totalAkhir / instrumens.length;
  }

  Future<void> _saveAssessment() async {
    if (_isSaving) return;
    setState(() => _isSaving = true);

    try {
      final pengetahuanPayload = <String, int?>{};
      final catatanPengetahuanPayload = <String, String>{};

      for (final entry in _pengetahuanValues.entries) {
        pengetahuanPayload[entry.key.toString()] = entry.value;
        catatanPengetahuanPayload[entry.key.toString()] =
            _pengetahuanNotes[entry.key]?.text.trim() ?? '';
      }

      final keterampilanPayload = <String, int?>{};
      for (final entry in _keterampilanValues.entries) {
        keterampilanPayload[entry.key.toString()] = entry.value;
      }

      _sikapData.catatan = _sikapCatatanController.text.trim();
      _bilingualData.catatan = _bilingualCatatanController.text.trim();

      await _ukkService.saveAssessment(
        ujianId: widget.exam.id,
        siswaId: widget.student.id,
        pengetahuan: pengetahuanPayload,
        catatanPengetahuan: catatanPengetahuanPayload,
        keterampilan: keterampilanPayload,
        sikap: _detail?.permissions.canGradeSikap == true ? _sikapData.toJson() : null,
        bilingual: _detail?.permissions.canGradeBilingual == true ? _bilingualData.toJson() : null,
      );

      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Row(
            children: [
              Icon(Icons.check_circle_rounded, color: Colors.white),
              SizedBox(width: 8),
              Expanded(child: Text('Penilaian UKK berhasil disimpan!')),
            ],
          ),
          backgroundColor: Color(0xFF15803D),
          duration: Duration(seconds: 2),
        ),
      );

      Navigator.of(context).pop(true);
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gagal menyimpan penilaian: $e'),
          backgroundColor: AppTheme.red,
        ),
      );
    } finally {
      if (mounted) setState(() => _isSaving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              widget.student.namaLengkap,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                color: AppTheme.ink,
                fontSize: 15,
                fontWeight: FontWeight.w800,
              ),
            ),
            Text(
              'NIS: ${widget.student.nis} · ${widget.student.rombelNama ?? ""}',
              style: TextStyle(color: Colors.grey.shade600, fontSize: 11),
            ),
          ],
        ),
        bottom: (_tabController != null && _tabTitles.length > 1)
            ? TabBar(
                controller: _tabController,
                isScrollable: true,
                indicatorColor: AppTheme.red,
                indicatorWeight: 3,
                labelColor: AppTheme.red,
                unselectedLabelColor: Colors.grey.shade600,
                labelStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                tabs: _tabTitles.map((title) => Tab(text: title)).toList(),
              )
            : null,
      ),
      body: _buildBody(),
      bottomNavigationBar: _buildBottomBar(),
    );
  }

  Widget _buildBody() {
    if (_loading) {
      return const Center(child: CircularProgressIndicator(color: AppTheme.red));
    }

    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(Icons.error_outline, size: 48, color: Colors.red.shade400),
              const SizedBox(height: 12),
              Text(
                'Terjadi kesalahan:\n$_error',
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 13),
              ),
              const SizedBox(height: 16),
              ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: AppTheme.red),
                onPressed: _loadAssessment,
                child: const Text('Coba Lagi', style: TextStyle(color: Colors.white)),
              ),
            ],
          ),
        ),
      );
    }

    if (_tabTitles.isEmpty) {
      return const Center(
        child: Text('Tidak ada aspek penilaian yang ditugaskan untuk penguji ini.'),
      );
    }

    if (_tabTitles.length == 1) {
      return _buildTabContent(_tabTitles.first);
    }

    return TabBarView(
      controller: _tabController,
      children: _tabTitles.map((title) => _buildTabContent(title)).toList(),
    );
  }

  Widget _buildTabContent(String tabName) {
    switch (tabName) {
      case 'Pengetahuan':
        return _buildPengetahuanTab();
      case 'Bilingual Rubrik':
        return _buildBilingualTab();
      case 'Keterampilan':
        return _buildKeterampilanTab();
      case 'Sikap Kerja':
        return _buildSikapTab();
      default:
        return const SizedBox.shrink();
    }
  }

  // --- Pengetahuan Tab ---
  Widget _buildPengetahuanTab() {
    final soals = _detail?.instrumens.expand((i) => i.soalPengetahuan).toList() ?? [];

    return ListView.separated(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
      itemCount: soals.length,
      separatorBuilder: (_, __) => const SizedBox(height: 14),
      itemBuilder: (context, index) {
        final soal = soals[index];
        final currentVal = _pengetahuanValues[soal.id];
        final noteController = _pengetahuanNotes[soal.id];

        return Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              color: currentVal == 1
                  ? Colors.green.shade300
                  : (currentVal == 0 ? Colors.red.shade300 : Colors.grey.shade200),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.02),
                blurRadius: 6,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: 28,
                    height: 28,
                    decoration: BoxDecoration(
                      color: AppTheme.red.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    alignment: Alignment.center,
                    child: Text(
                      soal.nomorSoal ?? '${index + 1}',
                      style: const TextStyle(
                        color: AppTheme.red,
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                      ),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (soal.jenisSoal != null)
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                            margin: const EdgeInsets.only(bottom: 4),
                            decoration: BoxDecoration(
                              color: soal.jenisSoal == 'bilingual'
                                  ? Colors.indigo.shade50
                                  : Colors.grey.shade100,
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: Text(
                              soal.jenisSoal!.toUpperCase(),
                              style: TextStyle(
                                fontSize: 10,
                                fontWeight: FontWeight.bold,
                                color: soal.jenisSoal == 'bilingual'
                                    ? Colors.indigo.shade800
                                    : Colors.grey.shade700,
                              ),
                            ),
                          ),
                        Text(
                          soal.pertanyaan,
                          style: const TextStyle(
                            color: AppTheme.ink,
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            height: 1.4,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              if (soal.kunciJawaban != null && soal.kunciJawaban!.isNotEmpty) ...[
                const SizedBox(height: 10),
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF8FAFC),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: Colors.grey.shade200),
                  ),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Icon(Icons.key_rounded, size: 14, color: Color(0xFFD97706)),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          'Kunci / Panduan: ${soal.kunciJawaban}',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey.shade800,
                            fontStyle: FontStyle.italic,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
              const SizedBox(height: 14),
              // Binary Choice: Benar (1) / Salah (0)
              Row(
                children: [
                  Expanded(
                    child: InkWell(
                      onTap: () {
                        setState(() {
                          _pengetahuanValues[soal.id] = (currentVal == 1) ? null : 1;
                        });
                      },
                      borderRadius: BorderRadius.circular(10),
                      child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 10),
                        decoration: BoxDecoration(
                          color: currentVal == 1
                              ? const Color(0xFF15803D)
                              : const Color(0xFFF0FDF4),
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(
                            color: currentVal == 1
                                ? const Color(0xFF15803D)
                                : const Color(0xFF86EFAC),
                          ),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              Icons.check_circle_rounded,
                              size: 16,
                              color: currentVal == 1 ? Colors.white : const Color(0xFF166534),
                            ),
                            const SizedBox(width: 6),
                            Text(
                              'Benar (1)',
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.bold,
                                color: currentVal == 1 ? Colors.white : const Color(0xFF166534),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: InkWell(
                      onTap: () {
                        setState(() {
                          _pengetahuanValues[soal.id] = (currentVal == 0) ? null : 0;
                        });
                      },
                      borderRadius: BorderRadius.circular(10),
                      child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 10),
                        decoration: BoxDecoration(
                          color: currentVal == 0
                              ? const Color(0xFFB91C1C)
                              : const Color(0xFFFEF2F2),
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(
                            color: currentVal == 0
                                ? const Color(0xFFB91C1C)
                                : const Color(0xFFFCA5A5),
                          ),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              Icons.cancel_rounded,
                              size: 16,
                              color: currentVal == 0 ? Colors.white : const Color(0xFF991B1B),
                            ),
                            const SizedBox(width: 6),
                            Text(
                              'Salah (0)',
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.bold,
                                color: currentVal == 0 ? Colors.white : const Color(0xFF991B1B),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              // Catatan Field
              TextField(
                controller: noteController,
                decoration: InputDecoration(
                  hintText: 'Catatan penguji (opsional)...',
                  isDense: true,
                  contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  fillColor: const Color(0xFFF8FAFC),
                  filled: true,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                    borderSide: BorderSide(color: Colors.grey.shade300),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                    borderSide: BorderSide(color: Colors.grey.shade300),
                  ),
                ),
                style: const TextStyle(fontSize: 12),
              ),
            ],
          ),
        );
      },
    );
  }

  // --- Bilingual Rubric Tab ---
  Widget _buildBilingualTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Option B Info Alert
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: Colors.indigo.shade50,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.indigo.shade200),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Icon(Icons.info_outline_rounded, size: 20, color: Colors.indigo.shade800),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Integrasi Penilaian Bilingual (Opsi B)',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.bold,
                          color: Colors.indigo.shade900,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'Skor Pengetahuan dihitung seimbang: 50% Konten Soal + 50% Kecakapan Bicara (Fluency, Pronunciation, Vocabulary, Comprehension).',
                        style: TextStyle(fontSize: 12, color: Colors.indigo.shade800, height: 1.3),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // 4 Rubric Cards
          _buildRubricCard(
            title: 'Fluency (Kelancaran)',
            description: 'Kelancaran penyampaian, jeda berpikir, dan tempo berbicara.',
            currentValue: _bilingualData.fluency,
            onChanged: (val) => setState(() => _bilingualData.fluency = val),
          ),
          const SizedBox(height: 12),
          _buildRubricCard(
            title: 'Pronunciation (Pelafalan)',
            description: 'Kejelasan pengucapan kata dan intonasi teknis kejuruan.',
            currentValue: _bilingualData.pronunciation,
            onChanged: (val) => setState(() => _bilingualData.pronunciation = val),
          ),
          const SizedBox(height: 12),
          _buildRubricCard(
            title: 'Vocabulary (Kosakata)',
            description: 'Kesesuaian dan kekayaan kosakata teknis dalam bahasa Inggris.',
            currentValue: _bilingualData.vocabulary,
            onChanged: (val) => setState(() => _bilingualData.vocabulary = val),
          ),
          const SizedBox(height: 12),
          _buildRubricCard(
            title: 'Comprehension (Pemahaman)',
            description: 'Kemampuan memahami pertanyaan dan menjawab secara relevan.',
            currentValue: _bilingualData.comprehension,
            onChanged: (val) => setState(() => _bilingualData.comprehension = val),
          ),
          const SizedBox(height: 16),

          // Kesimpulan / Catatan Bilingual
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: Colors.grey.shade200),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Catatan Evaluasi Wawancara Bilingual',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: AppTheme.ink,
                  ),
                ),
                const SizedBox(height: 8),
                TextField(
                  controller: _bilingualCatatanController,
                  maxLines: 3,
                  decoration: InputDecoration(
                    hintText: 'Tuliskan catatan kemajuan atau rekomendasi kemampuan bahasa Inggris siswa...',
                    fillColor: const Color(0xFFF8FAFC),
                    filled: true,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: BorderSide(color: Colors.grey.shade300),
                    ),
                    enabledBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: BorderSide(color: Colors.grey.shade300),
                    ),
                  ),
                  style: const TextStyle(fontSize: 13),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRubricCard({
    required String title,
    required String description,
    required String? currentValue,
    required ValueChanged<String?> onChanged,
  }) {
    final options = [
      ('poor', 'Poor (1)', const Color(0xFFDC2626)),
      ('fair', 'Fair (2)', const Color(0xFFD97706)),
      ('good', 'Good (3)', const Color(0xFF2563EB)),
      ('excellent', 'Excellent (4)', const Color(0xFF16A34A)),
    ];

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: AppTheme.ink,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            description,
            style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
          ),
          const SizedBox(height: 12),
          Row(
            children: options.map((opt) {
              final isSelected = currentValue == opt.$1;
              return Expanded(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 3),
                  child: InkWell(
                    onTap: () => onChanged(isSelected ? null : opt.$1),
                    borderRadius: BorderRadius.circular(8),
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 9),
                      decoration: BoxDecoration(
                        color: isSelected ? opt.$3 : opt.$3.withValues(alpha: 0.08),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(
                          color: isSelected ? opt.$3 : opt.$3.withValues(alpha: 0.3),
                        ),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        opt.$2,
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: isSelected ? Colors.white : opt.$3,
                        ),
                      ),
                    ),
                  ),
                ),
              );
            }).toList(),
          ),
        ],
      ),
    );
  }

  // --- Keterampilan Tab ---
  Widget _buildKeterampilanTab() {
    final categories = _detail?.instrumens.expand((i) => i.kategoriKeterampilan).toList() ?? [];

    return ListView.separated(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
      itemCount: categories.length,
      separatorBuilder: (_, __) => const SizedBox(height: 16),
      itemBuilder: (context, catIndex) {
        final cat = categories[catIndex];

        return Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: Colors.grey.shade200),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.02),
                blurRadius: 6,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: const Color(0xFF0F766E).withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Text(
                      'Kategori ${catIndex + 1}',
                      style: const TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF0F766E),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      cat.namaKategori,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                        color: AppTheme.ink,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 14),
              const Divider(height: 1),
              const SizedBox(height: 10),
              // Indicators list
              ...cat.indikators.map((ind) {
                final currentVal = _keterampilanValues[ind.id];
                return Padding(
                  padding: const EdgeInsets.symmetric(vertical: 8),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        '${ind.nomorIndikator ?? "•"} ${ind.namaIndikator}',
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.ink,
                          height: 1.3,
                        ),
                      ),
                      const SizedBox(height: 8),
                      // 0 - 3 Rating scale
                      Row(
                        children: [0, 1, 2, 3].map((val) {
                          final isSelected = currentVal == val;
                          Color color;
                          String label;
                          switch (val) {
                            case 3:
                              color = const Color(0xFF15803D);
                              label = '3 - Sangat Baik';
                              break;
                            case 2:
                              color = const Color(0xFF0284C7);
                              label = '2 - Baik';
                              break;
                            case 1:
                              color = const Color(0xFFD97706);
                              label = '1 - Cukup';
                              break;
                            default:
                              color = const Color(0xFFDC2626);
                              label = '0 - Tidak';
                          }

                          return Expanded(
                            child: Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 2.5),
                              child: InkWell(
                                onTap: () {
                                  setState(() {
                                    _keterampilanValues[ind.id] = isSelected ? null : val;
                                  });
                                },
                                borderRadius: BorderRadius.circular(8),
                                child: Container(
                                  padding: const EdgeInsets.symmetric(vertical: 8),
                                  decoration: BoxDecoration(
                                    color: isSelected ? color : color.withValues(alpha: 0.08),
                                    borderRadius: BorderRadius.circular(8),
                                    border: Border.all(
                                      color: isSelected ? color : color.withValues(alpha: 0.3),
                                    ),
                                  ),
                                  alignment: Alignment.center,
                                  child: Text(
                                    label,
                                    style: TextStyle(
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                      color: isSelected ? Colors.white : color,
                                    ),
                                    textAlign: TextAlign.center,
                                  ),
                                ),
                              ),
                            ),
                          );
                        }).toList(),
                      ),
                      const SizedBox(height: 6),
                      const Divider(height: 1),
                    ],
                  ),
                );
              }),
            ],
          ),
        );
      },
    );
  }

  // --- Sikap Kerja Tab ---
  Widget _buildSikapTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _buildSikapAspectCard(
            title: '1. Kerapian',
            description: 'Kerapian seragam, tempat kerja, dan penataan alat.',
            currentValue: _sikapData.kerapian,
            onChanged: (val) => setState(() => _sikapData.kerapian = val),
          ),
          const SizedBox(height: 12),
          _buildSikapAspectCard(
            title: '2. Ketelitian',
            description: 'Ketelitian dalam proses pengerjaan dan pengecekan hasil.',
            currentValue: _sikapData.ketelitian,
            onChanged: (val) => setState(() => _sikapData.ketelitian = val),
          ),
          const SizedBox(height: 12),
          _buildSikapAspectCard(
            title: '3. Kedisiplinan',
            description: 'Ketaatan terhadap SOP, waktu pelaksanaan, dan instruksi.',
            currentValue: _sikapData.kedisiplinan,
            onChanged: (val) => setState(() => _sikapData.kedisiplinan = val),
          ),
          const SizedBox(height: 12),
          _buildSikapAspectCard(
            title: '4. Tanggung Jawab',
            description: 'Menjaga keselamatan kerja, fasilitas, dan menyelesaikan tugas.',
            currentValue: _sikapData.tanggungJawab,
            onChanged: (val) => setState(() => _sikapData.tanggungJawab = val),
          ),
          const SizedBox(height: 12),
          _buildSikapAspectCard(
            title: '5. Inisiatif',
            description: 'Proaktif dan tanggap dalam mengatasi kendala di tempat kerja.',
            currentValue: _sikapData.inisiatif,
            onChanged: (val) => setState(() => _sikapData.inisiatif = val),
          ),
          const SizedBox(height: 16),
          // Catatan Sikap
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: Colors.grey.shade200),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Catatan Khusus Sikap Kerja',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: AppTheme.ink,
                  ),
                ),
                const SizedBox(height: 8),
                TextField(
                  controller: _sikapCatatanController,
                  maxLines: 3,
                  decoration: InputDecoration(
                    hintText: 'Tuliskan catatan sikap atau perilaku siswa selama ujian...',
                    fillColor: const Color(0xFFF8FAFC),
                    filled: true,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: BorderSide(color: Colors.grey.shade300),
                    ),
                    enabledBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: BorderSide(color: Colors.grey.shade300),
                    ),
                  ),
                  style: const TextStyle(fontSize: 13),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSikapAspectCard({
    required String title,
    required String description,
    required String? currentValue,
    required ValueChanged<String?> onChanged,
  }) {
    final options = [
      ('kurang', 'Kurang', const Color(0xFFDC2626)),
      ('cukup', 'Cukup', const Color(0xFFD97706)),
      ('baik', 'Baik', const Color(0xFF2563EB)),
      ('sangat_baik', 'Sgt Baik', const Color(0xFF16A34A)),
    ];

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
              color: AppTheme.ink,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            description,
            style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
          ),
          const SizedBox(height: 10),
          Row(
            children: options.map((opt) {
              final isSelected = currentValue == opt.$1;
              return Expanded(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 3),
                  child: InkWell(
                    onTap: () => onChanged(isSelected ? null : opt.$1),
                    borderRadius: BorderRadius.circular(8),
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 8),
                      decoration: BoxDecoration(
                        color: isSelected ? opt.$3 : opt.$3.withValues(alpha: 0.08),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(
                          color: isSelected ? opt.$3 : opt.$3.withValues(alpha: 0.3),
                        ),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        opt.$2,
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: isSelected ? Colors.white : opt.$3,
                        ),
                      ),
                    ),
                  ),
                ),
              );
            }).toList(),
          ),
        ],
      ),
    );
  }

  // --- Sticky Bottom Bar for Live Scores & Save ---
  Widget _buildBottomBar() {
    if (_loading || _error != null) return const SizedBox.shrink();

    final skorP = _skorPFinal;
    final skorK = _skorKFinal;
    final na = _nilaiAkhirFinal;

    return Container(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 16),
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: Colors.grey.shade200)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.06),
            blurRadius: 10,
            offset: const Offset(0, -3),
          ),
        ],
      ),
      child: SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Live Score Summary Row
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildScorePill('Teori', skorP.toStringAsFixed(1), Colors.purple),
                _buildScorePill('Praktik', skorK.toStringAsFixed(1), Colors.teal),
                _buildScorePill('Nilai Akhir', na.toStringAsFixed(1), AppTheme.red, isHighlight: true),
              ],
            ),
            const SizedBox(height: 12),
            // Save Button
            SizedBox(
              width: double.infinity,
              height: 48,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.red,
                  foregroundColor: Colors.white,
                  elevation: 0,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                onPressed: _isSaving ? null : _saveAssessment,
                child: _isSaving
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(strokeWidth: 2.5, color: Colors.white),
                      )
                    : const Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.save_rounded, size: 20),
                          SizedBox(width: 8),
                          Text(
                            'Simpan Penilaian UKK',
                            style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
                          ),
                        ],
                      ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildScorePill(String title, String score, Color color, {bool isHighlight = false}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
      decoration: BoxDecoration(
        color: isHighlight ? color.withValues(alpha: 0.12) : Colors.grey.shade100,
        borderRadius: BorderRadius.circular(8),
        border: isHighlight ? Border.all(color: color.withValues(alpha: 0.3)) : null,
      ),
      child: Column(
        children: [
          Text(
            title,
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w600,
              color: isHighlight ? color : Colors.grey.shade600,
            ),
          ),
          const SizedBox(height: 1),
          Text(
            score,
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w800,
              color: isHighlight ? color : AppTheme.ink,
            ),
          ),
        ],
      ),
    );
  }
}
