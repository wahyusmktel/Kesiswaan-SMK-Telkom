import 'package:flutter/material.dart';

import '../../../core/theme/app_theme.dart';
import '../../../data/models/ukk/ukk_models.dart';
import '../../../data/services/ukk_service.dart';
import 'ukk_assessment_screen.dart';

class UkkStudentListScreen extends StatefulWidget {
  const UkkStudentListScreen({required this.exam, super.key});

  final UkkExamItem exam;

  @override
  State<UkkStudentListScreen> createState() => _UkkStudentListScreenState();
}

class _UkkStudentListScreenState extends State<UkkStudentListScreen> {
  final _ukkService = UkkService();
  final _searchController = TextEditingController();

  int? _selectedRombelId;
  String _filterStatus = 'all'; // 'all', 'pending', 'completed'
  String _searchQuery = '';

  bool _loading = true;
  String? _error;
  List<UkkRombelItem> _rombels = [];
  List<UkkStudentItem> _students = [];

  @override
  void initState() {
    super.initState();
    _loadData();
    _searchController.addListener(() {
      setState(() {
        _searchQuery = _searchController.text.trim().toLowerCase();
      });
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      final res = await _ukkService.getStudents(
        widget.exam.id,
        rombelId: _selectedRombelId,
      );
      if (!mounted) return;
      setState(() {
        _rombels = res.rombels;
        _students = res.siswas;
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

  List<UkkStudentItem> get _filteredStudents {
    return _students.where((s) {
      if (_searchQuery.isNotEmpty) {
        final matchesName = s.namaLengkap.toLowerCase().contains(_searchQuery);
        final matchesNis = s.nis.toLowerCase().contains(_searchQuery);
        if (!matchesName && !matchesNis) return false;
      }

      if (_filterStatus == 'pending') {
        return !s.isCompleted;
      } else if (_filterStatus == 'completed') {
        return s.isCompleted;
      }

      return true;
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    final completedCount = _students.where((s) => s.isCompleted).length;
    final pendingCount = _students.length - completedCount;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              widget.exam.namaUjian,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                color: AppTheme.ink,
                fontSize: 16,
                fontWeight: FontWeight.w800,
              ),
            ),
            Text(
              'Daftar Siswa Pengujian · ${widget.exam.isBilingual ? "Bilingual" : "Reguler"}',
              style: TextStyle(
                color: Colors.grey.shade600,
                fontSize: 12,
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: AppTheme.ink),
            onPressed: _loadData,
          ),
        ],
      ),
      body: Column(
        children: [
          // Filter & Search Header
          Container(
            color: Colors.white,
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Rombel Dropdown Filter (if multiple rombels available)
                if (_rombels.length > 1) ...[
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    decoration: BoxDecoration(
                      color: const Color(0xFFF1F5F9),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: Colors.grey.shade300),
                    ),
                    child: DropdownButtonHideUnderline(
                      child: DropdownButton<int?>(
                        value: _selectedRombelId,
                        isExpanded: true,
                        hint: const Text('Semua Kelas / Rombel'),
                        icon: const Icon(Icons.arrow_drop_down_rounded),
                        items: [
                          const DropdownMenuItem<int?>(
                            value: null,
                            child: Text(
                              'Semua Kelas / Rombel',
                              style: TextStyle(fontWeight: FontWeight.w600),
                            ),
                          ),
                          ..._rombels.map((r) => DropdownMenuItem<int?>(
                                value: r.id,
                                child: Text('Kelas ${r.namaKelas}'),
                              )),
                        ],
                        onChanged: (val) {
                          setState(() => _selectedRombelId = val);
                          _loadData();
                        },
                      ),
                    ),
                  ),
                  const SizedBox(height: 10),
                ],

                // Search Bar
                TextField(
                  controller: _searchController,
                  decoration: InputDecoration(
                    hintText: 'Cari nama siswa atau NIS...',
                    prefixIcon: const Icon(Icons.search_rounded, size: 20),
                    suffixIcon: _searchQuery.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.clear_rounded, size: 18),
                            onPressed: () => _searchController.clear(),
                          )
                        : null,
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(vertical: 10, horizontal: 14),
                    fillColor: const Color(0xFFF8FAFC),
                    filled: true,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(10),
                      borderSide: BorderSide(color: Colors.grey.shade300),
                    ),
                    enabledBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(10),
                      borderSide: BorderSide(color: Colors.grey.shade300),
                    ),
                  ),
                ),
                const SizedBox(height: 10),

                // Quick Status Segmented Chips
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      _buildFilterChip('all', 'Semua (${_students.length})'),
                      const SizedBox(width: 8),
                      _buildFilterChip('pending', 'Belum Selesai ($pendingCount)', color: Colors.orange.shade800),
                      const SizedBox(width: 8),
                      _buildFilterChip('completed', 'Selesai ($completedCount)', color: Colors.green.shade800),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1),

          // Student List / Body
          Expanded(
            child: _buildBody(),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String key, String label, {Color? color}) {
    final isSelected = _filterStatus == key;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (_) => setState(() => _filterStatus = key),
      selectedColor: (color ?? AppTheme.red).withValues(alpha: 0.12),
      labelStyle: TextStyle(
        fontSize: 12,
        fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
        color: isSelected ? (color ?? AppTheme.red) : Colors.grey.shade700,
      ),
      side: BorderSide(
        color: isSelected ? (color ?? AppTheme.red) : Colors.grey.shade300,
      ),
      backgroundColor: Colors.white,
    );
  }

  Widget _buildBody() {
    if (_loading) {
      return const Center(
        child: CircularProgressIndicator(color: AppTheme.red),
      );
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
                onPressed: _loadData,
                child: const Text('Coba Lagi', style: TextStyle(color: Colors.white)),
              ),
            ],
          ),
        ),
      );
    }

    final filtered = _filteredStudents;
    if (filtered.isEmpty) {
      return Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.person_search_outlined, size: 54, color: Colors.grey.shade400),
            const SizedBox(height: 12),
            Text(
              _searchQuery.isNotEmpty
                  ? 'Tidak ada siswa yang sesuai pencarian'
                  : 'Belum ada data siswa untuk kriteria ini',
              style: TextStyle(color: Colors.grey.shade600, fontSize: 14),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadData,
      child: ListView.separated(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        itemCount: filtered.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (context, index) {
          final student = filtered[index];
          return _buildStudentCard(student);
        },
      ),
    );
  }

  Widget _buildStudentCard(UkkStudentItem s) {
    Color statusColor;
    String statusLabel;
    IconData statusIcon;

    if (s.isCompleted) {
      statusColor = const Color(0xFF15803D);
      statusLabel = 'Selesai';
      statusIcon = Icons.check_circle_rounded;
    } else if (s.status == 'sebagian') {
      statusColor = const Color(0xFF0369A1);
      statusLabel = 'Sebagian';
      statusIcon = Icons.pending_rounded;
    } else {
      statusColor = const Color(0xFFC2410C);
      statusLabel = 'Belum';
      statusIcon = Icons.radio_button_unchecked_rounded;
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: s.isCompleted ? Colors.green.shade200 : Colors.grey.shade200,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        borderRadius: BorderRadius.circular(14),
        child: InkWell(
          borderRadius: BorderRadius.circular(14),
          onTap: () async {
            final refreshed = await Navigator.of(context).push<bool>(
              MaterialPageRoute(
                builder: (_) => UkkAssessmentScreen(
                  exam: widget.exam,
                  student: s,
                ),
              ),
            );
            if (refreshed == true && mounted) {
              _loadData();
            }
          },
          child: Padding(
            padding: const EdgeInsets.all(14),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Student Avatar / Photo
                ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: s.foto != null
                      ? Image.network(
                          s.foto!,
                          width: 48,
                          height: 48,
                          fit: BoxFit.cover,
                          errorBuilder: (_, __, ___) => _buildAvatarFallback(s),
                        )
                      : _buildAvatarFallback(s),
                ),
                const SizedBox(width: 12),

                // Student Info
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          if (s.nomorUrut != null)
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                              margin: const EdgeInsets.only(right: 6),
                              decoration: BoxDecoration(
                                color: Colors.grey.shade100,
                                borderRadius: BorderRadius.circular(4),
                              ),
                              child: Text(
                                '#${s.nomorUrut}',
                                style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.grey.shade700,
                                ),
                              ),
                            ),
                          Expanded(
                            child: Text(
                              s.namaLengkap,
                              style: const TextStyle(
                                color: AppTheme.ink,
                                fontSize: 14,
                                fontWeight: FontWeight.w700,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 3),
                      Text(
                        'NIS: ${s.nis}${s.rombelNama != null ? " · ${s.rombelNama!}" : ""}',
                        style: TextStyle(
                          color: Colors.grey.shade600,
                          fontSize: 12,
                        ),
                      ),
                      const SizedBox(height: 8),

                      // Progress Pills
                      Wrap(
                        spacing: 6,
                        runSpacing: 4,
                        children: [
                          if (s.totalPengetahuan > 0)
                            _buildProgressBadge(
                              'Teori: ${s.gradedPengetahuan}/${s.totalPengetahuan}',
                              s.gradedPengetahuan >= s.totalPengetahuan,
                            ),
                          if (s.totalKeterampilan > 0)
                            _buildProgressBadge(
                              'Praktik: ${s.gradedKeterampilan}/${s.totalKeterampilan}',
                              s.gradedKeterampilan >= s.totalKeterampilan,
                            ),
                          if (widget.exam.peranPenguji != 'pengetahuan')
                            _buildProgressBadge(
                              s.hasSikap ? 'Sikap ✓' : 'Sikap -',
                              s.hasSikap,
                            ),
                          if (widget.exam.isBilingual && widget.exam.peranPenguji != 'keterampilan')
                            _buildProgressBadge(
                              s.hasBilingual ? 'Bilingual ✓' : 'Bilingual -',
                              s.hasBilingual,
                            ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 8),

                // Status Badge & Arrow
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(
                        color: statusColor.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(statusIcon, size: 12, color: statusColor),
                          const SizedBox(width: 4),
                          Text(
                            statusLabel,
                            style: TextStyle(
                              color: statusColor,
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),
                    const Icon(
                      Icons.chevron_right_rounded,
                      size: 20,
                      color: Colors.grey,
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildAvatarFallback(UkkStudentItem s) {
    return Container(
      width: 48,
      height: 48,
      decoration: BoxDecoration(
        color: s.isCompleted ? const Color(0xFFDCFCE7) : const Color(0xFFF1F5F9),
        borderRadius: BorderRadius.circular(12),
      ),
      alignment: Alignment.center,
      child: Text(
        s.namaLengkap.isNotEmpty ? s.namaLengkap[0].toUpperCase() : 'S',
        style: TextStyle(
          color: s.isCompleted ? const Color(0xFF166534) : AppTheme.ink,
          fontSize: 18,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }

  Widget _buildProgressBadge(String text, bool isOk) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(
        color: isOk ? Colors.green.shade50 : Colors.grey.shade100,
        borderRadius: BorderRadius.circular(4),
        border: Border.all(
          color: isOk ? Colors.green.shade200 : Colors.grey.shade300,
          width: 0.8,
        ),
      ),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 10,
          fontWeight: FontWeight.w600,
          color: isOk ? Colors.green.shade800 : Colors.grey.shade700,
        ),
      ),
    );
  }
}
