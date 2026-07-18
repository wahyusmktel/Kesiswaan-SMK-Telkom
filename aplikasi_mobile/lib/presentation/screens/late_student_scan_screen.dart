import 'dart:async';

import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';

import '../../core/theme/app_theme.dart';
import '../../data/models/student.dart';
import '../../data/services/api_client.dart';
import '../../data/services/security_service.dart';

enum _InputMode { scanner, search }

class LateStudentScanScreen extends StatefulWidget {
  const LateStudentScanScreen({
    required this.isActive,
    required this.onRecorded,
    super.key,
  });

  final bool isActive;
  final VoidCallback onRecorded;

  @override
  State<LateStudentScanScreen> createState() => _LateStudentScanScreenState();
}

class _LateStudentScanScreenState extends State<LateStudentScanScreen> {
  final _service = SecurityService();
  final _scannerController = MobileScannerController(
    autoStart: false,
    detectionSpeed: DetectionSpeed.noDuplicates,
  );
  final _searchController = TextEditingController();
  Timer? _searchDebounce;

  _InputMode _mode = _InputMode.scanner;
  bool _processingCode = false;
  bool _searching = false;
  List<Student> _searchResults = const [];
  String? _error;

  @override
  void initState() {
    super.initState();
    if (widget.isActive) {
      WidgetsBinding.instance.addPostFrameCallback((_) => _startScanner());
    }
  }

  @override
  void didUpdateWidget(covariant LateStudentScanScreen oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.isActive == oldWidget.isActive) return;
    if (widget.isActive && _mode == _InputMode.scanner) {
      _startScanner();
    } else {
      _scannerController.stop();
    }
  }

  @override
  void dispose() {
    _searchDebounce?.cancel();
    _searchController.dispose();
    _scannerController.dispose();
    super.dispose();
  }

  Future<void> _startScanner() async {
    if (!widget.isActive || _mode != _InputMode.scanner) return;
    try {
      await _scannerController.start();
    } catch (_) {
      if (mounted) {
        setState(() {
          _error =
              'Kamera belum dapat dibuka. Izinkan akses kamera atau gunakan pencarian manual.';
        });
      }
    }
  }

  Future<void> _changeMode(_InputMode mode) async {
    if (_mode == mode) return;
    FocusScope.of(context).unfocus();
    setState(() {
      _mode = mode;
      _error = null;
    });
    if (mode == _InputMode.scanner) {
      await _startScanner();
    } else {
      await _scannerController.stop();
    }
  }

  Future<void> _onDetect(BarcodeCapture capture) async {
    if (_processingCode || capture.barcodes.isEmpty) return;
    final code = capture.barcodes.first.rawValue?.trim();
    if (code == null || code.isEmpty) return;

    setState(() {
      _processingCode = true;
      _error = null;
    });
    await _scannerController.stop();

    try {
      final student = await _service.scanStudent(code);
      if (!mounted) return;
      await _openStudent(student);
    } on ApiException catch (error) {
      if (mounted) setState(() => _error = error.message);
    } catch (_) {
      if (mounted) {
        setState(() => _error = 'Kode siswa belum dapat diperiksa.');
      }
    } finally {
      if (mounted) {
        setState(() => _processingCode = false);
        await _startScanner();
      }
    }
  }

  void _scheduleSearch(String value) {
    _searchDebounce?.cancel();
    final query = value.trim();
    if (query.length < 2) {
      setState(() {
        _searchResults = const [];
        _error = null;
      });
      return;
    }
    _searchDebounce = Timer(
      const Duration(milliseconds: 450),
      () => _searchStudents(query),
    );
  }

  Future<void> _searchStudents(String query) async {
    setState(() {
      _searching = true;
      _error = null;
    });
    try {
      final results = await _service.searchStudents(query);
      if (mounted && _searchController.text.trim() == query) {
        setState(() => _searchResults = results);
      }
    } on ApiException catch (error) {
      if (mounted) setState(() => _error = error.message);
    } finally {
      if (mounted) setState(() => _searching = false);
    }
  }

  Future<void> _openStudent(Student student) async {
    await showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      useSafeArea: true,
      builder: (context) => _LatenessForm(
        student: student,
        service: _service,
        onRecorded: () {
          widget.onRecorded();
          setState(() {
            _searchResults = _searchResults
                .map(
                  (item) => item.id == student.id
                      ? Student(
                          id: item.id,
                          nis: item.nis,
                          name: item.name,
                          className: item.className,
                          alreadyLateToday: true,
                          gender: item.gender,
                          major: item.major,
                        )
                      : item,
                )
                .toList();
          });
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Pendataan Terlambat'),
            Text(
              'Scan identitas siswa',
              style: TextStyle(
                color: Color(0xFF64748B),
                fontSize: 12,
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ),
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 12, 20, 14),
            child: SegmentedButton<_InputMode>(
              segments: const [
                ButtonSegment(
                  value: _InputMode.scanner,
                  icon: Icon(Icons.qr_code_scanner_rounded),
                  label: Text('Scan QR'),
                ),
                ButtonSegment(
                  value: _InputMode.search,
                  icon: Icon(Icons.search_rounded),
                  label: Text('Cari siswa'),
                ),
              ],
              selected: {_mode},
              onSelectionChanged: (selection) => _changeMode(selection.first),
              showSelectedIcon: false,
            ),
          ),
          if (_error != null)
            Container(
              width: double.infinity,
              margin: const EdgeInsets.fromLTRB(20, 0, 20, 14),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFFFEBEC),
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: const Color(0xFFFDA4AF)),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Icon(
                    Icons.error_outline,
                    color: AppTheme.red,
                    size: 20,
                  ),
                  const SizedBox(width: 10),
                  Expanded(child: Text(_error!)),
                ],
              ),
            ),
          Expanded(
            child: _mode == _InputMode.scanner
                ? _ScannerPanel(
                    controller: _scannerController,
                    processing: _processingCode,
                    onDetect: _onDetect,
                  )
                : _SearchPanel(
                    controller: _searchController,
                    searching: _searching,
                    results: _searchResults,
                    onChanged: _scheduleSearch,
                    onSelected: _openStudent,
                  ),
          ),
        ],
      ),
    );
  }
}

class _ScannerPanel extends StatelessWidget {
  const _ScannerPanel({
    required this.controller,
    required this.processing,
    required this.onDetect,
  });

  final MobileScannerController controller;
  final bool processing;
  final void Function(BarcodeCapture) onDetect;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
      child: Column(
        children: [
          Expanded(
            child: ClipRRect(
              borderRadius: BorderRadius.circular(8),
              child: Stack(
                fit: StackFit.expand,
                children: [
                  ColoredBox(
                    color: Colors.black,
                    child: MobileScanner(
                      controller: controller,
                      onDetect: onDetect,
                      errorBuilder: (context, error) => Center(
                        child: Padding(
                          padding: const EdgeInsets.all(24),
                          child: Text(
                            'Kamera tidak tersedia.\n${error.errorDetails?.message ?? ''}',
                            textAlign: TextAlign.center,
                            style: const TextStyle(color: Colors.white),
                          ),
                        ),
                      ),
                    ),
                  ),
                  Center(
                    child: Container(
                      width: 230,
                      height: 230,
                      decoration: BoxDecoration(
                        border: Border.all(color: Colors.white, width: 3),
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                  ),
                  Positioned(
                    right: 12,
                    top: 12,
                    child: IconButton.filledTonal(
                      tooltip: 'Nyalakan lampu',
                      onPressed: controller.toggleTorch,
                      icon: const Icon(Icons.flashlight_on_outlined),
                    ),
                  ),
                  if (processing)
                    const ColoredBox(
                      color: Color(0x99000000),
                      child: Center(
                        child: CircularProgressIndicator(color: Colors.white),
                      ),
                    ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 14),
          const Row(
            children: [
              Icon(Icons.info_outline, size: 20, color: Color(0xFF64748B)),
              SizedBox(width: 10),
              Expanded(
                child: Text(
                  'Arahkan QR siswa ke dalam kotak. Data siswa akan diperiksa sebelum disimpan.',
                  style: TextStyle(color: Color(0xFF64748B), fontSize: 12),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _SearchPanel extends StatelessWidget {
  const _SearchPanel({
    required this.controller,
    required this.searching,
    required this.results,
    required this.onChanged,
    required this.onSelected,
  });

  final TextEditingController controller;
  final bool searching;
  final List<Student> results;
  final ValueChanged<String> onChanged;
  final ValueChanged<Student> onSelected;

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20),
          child: TextField(
            controller: controller,
            autofocus: false,
            textInputAction: TextInputAction.search,
            onChanged: onChanged,
            decoration: InputDecoration(
              labelText: 'NIS atau nama siswa',
              hintText: 'Ketik minimal 2 karakter',
              prefixIcon: const Icon(Icons.search_rounded),
              suffixIcon: searching
                  ? const Padding(
                      padding: EdgeInsets.all(14),
                      child: SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      ),
                    )
                  : null,
            ),
          ),
        ),
        const SizedBox(height: 12),
        Expanded(
          child: results.isEmpty
              ? const Center(
                  child: Padding(
                    padding: EdgeInsets.all(28),
                    child: Text(
                      'Hasil pencarian siswa akan tampil di sini.',
                      textAlign: TextAlign.center,
                      style: TextStyle(color: Color(0xFF64748B)),
                    ),
                  ),
                )
              : ListView.separated(
                  padding: const EdgeInsets.fromLTRB(20, 0, 20, 24),
                  itemCount: results.length,
                  separatorBuilder: (_, _) => const Divider(height: 1),
                  itemBuilder: (context, index) {
                    final student = results[index];
                    return ListTile(
                      contentPadding: const EdgeInsets.symmetric(vertical: 4),
                      leading: CircleAvatar(
                        child: Text(
                          student.name.isEmpty
                              ? '?'
                              : student.name[0].toUpperCase(),
                        ),
                      ),
                      title: Text(student.name),
                      subtitle: Text('${student.nis} • ${student.className}'),
                      trailing: student.alreadyLateToday
                          ? const Icon(
                              Icons.check_circle,
                              color: AppTheme.success,
                            )
                          : const Icon(Icons.chevron_right_rounded),
                      onTap: () => onSelected(student),
                    );
                  },
                ),
        ),
      ],
    );
  }
}

class _LatenessForm extends StatefulWidget {
  const _LatenessForm({
    required this.student,
    required this.service,
    required this.onRecorded,
  });

  final Student student;
  final SecurityService service;
  final VoidCallback onRecorded;

  @override
  State<_LatenessForm> createState() => _LatenessFormState();
}

class _LatenessFormState extends State<_LatenessForm> {
  final _formKey = GlobalKey<FormState>();
  final _reasonController = TextEditingController();
  bool _submitting = false;
  String? _error;

  @override
  void dispose() {
    _reasonController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate() || _submitting) return;
    setState(() {
      _submitting = true;
      _error = null;
    });
    try {
      await widget.service.recordLateness(
        studentId: widget.student.id,
        reason: _reasonController.text.trim(),
      );
      widget.onRecorded();
      if (!mounted) return;
      final messenger = ScaffoldMessenger.of(context);
      Navigator.pop(context);
      messenger.showSnackBar(
        const SnackBar(
          content: Text(
            'Keterlambatan berhasil dicatat. Arahkan siswa ke ruang piket.',
          ),
        ),
      );
    } on ApiException catch (error) {
      if (mounted) setState(() => _error = error.message);
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.fromLTRB(
        20,
        16,
        20,
        MediaQuery.viewInsetsOf(context).bottom + 20,
      ),
      child: SingleChildScrollView(
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Center(
                child: Container(
                  width: 44,
                  height: 4,
                  decoration: BoxDecoration(
                    color: const Color(0xFFCBD5E1),
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 20),
              Text(
                widget.student.name,
                style: Theme.of(context).textTheme.titleLarge,
              ),
              const SizedBox(height: 4),
              Text(
                '${widget.student.nis} • ${widget.student.className}',
                style: const TextStyle(color: Color(0xFF64748B)),
              ),
              const SizedBox(height: 20),
              if (widget.student.alreadyLateToday)
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: const Color(0xFFECFDF5),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: const Color(0xFF6EE7B7)),
                  ),
                  child: const Row(
                    children: [
                      Icon(Icons.check_circle, color: AppTheme.success),
                      SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          'Siswa ini sudah didata terlambat hari ini.',
                        ),
                      ),
                    ],
                  ),
                )
              else ...[
                if (_error != null) ...[
                  Text(
                    _error!,
                    style: const TextStyle(
                      color: AppTheme.red,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 12),
                ],
                TextFormField(
                  controller: _reasonController,
                  autofocus: false,
                  maxLines: 3,
                  maxLength: 1000,
                  decoration: const InputDecoration(
                    labelText: 'Alasan keterlambatan',
                    hintText:
                        'Contoh: Kendaraan mengalami kendala di perjalanan',
                    alignLabelWithHint: true,
                  ),
                  validator: (value) {
                    if ((value ?? '').trim().length < 5) {
                      return 'Tuliskan alasan minimal 5 karakter.';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 8),
                FilledButton.icon(
                  onPressed: _submitting ? null : _submit,
                  icon: _submitting
                      ? const SizedBox(
                          width: 18,
                          height: 18,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            color: Colors.white,
                          ),
                        )
                      : const Icon(Icons.save_outlined),
                  label: Text(
                    _submitting ? 'Menyimpan...' : 'Simpan Keterlambatan',
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
