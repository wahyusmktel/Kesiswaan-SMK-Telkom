import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../../data/models/lateness_record.dart';
import '../../data/services/api_client.dart';
import '../../data/services/security_service.dart';

class LatenessHistoryScreen extends StatefulWidget {
  const LatenessHistoryScreen({super.key});

  @override
  State<LatenessHistoryScreen> createState() => _LatenessHistoryScreenState();
}

class _LatenessHistoryScreenState extends State<LatenessHistoryScreen> {
  final _service = SecurityService();
  late Future<List<LatenessRecord>> _records;

  @override
  void initState() {
    super.initState();
    _records = _service.getTodayHistory();
  }

  Future<void> _refresh() async {
    final request = _service.getTodayHistory();
    setState(() => _records = request);
    await request;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Riwayat Hari Ini')),
      body: RefreshIndicator(
        onRefresh: _refresh,
        child: FutureBuilder<List<LatenessRecord>>(
          future: _records,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator());
            }
            if (snapshot.hasError) {
              final message = snapshot.error is ApiException
                  ? (snapshot.error! as ApiException).message
                  : 'Riwayat belum dapat dimuat.';
              return ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(24),
                children: [
                  const SizedBox(height: 80),
                  Text(message, textAlign: TextAlign.center),
                  const SizedBox(height: 16),
                  FilledButton(
                    onPressed: _refresh,
                    child: const Text('Coba lagi'),
                  ),
                ],
              );
            }

            final records = snapshot.data!;
            if (records.isEmpty) {
              return ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(24),
                children: const [
                  SizedBox(height: 100),
                  Icon(Icons.history_toggle_off_rounded, size: 52),
                  SizedBox(height: 16),
                  Text(
                    'Belum ada pendataan keterlambatan hari ini.',
                    textAlign: TextAlign.center,
                  ),
                ],
              );
            }

            return ListView.separated(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(20),
              itemCount: records.length,
              separatorBuilder: (_, _) => const Divider(height: 1),
              itemBuilder: (context, index) {
                final record = records[index];
                return ListTile(
                  contentPadding: const EdgeInsets.symmetric(vertical: 6),
                  title: Text(record.student.name),
                  subtitle: Text(
                    '${record.student.nis} • ${record.student.className}\n${record.reason}',
                  ),
                  isThreeLine: true,
                  trailing: Text(
                    DateFormat('HH:mm').format(record.recordedAt.toLocal()),
                    style: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                );
              },
            );
          },
        ),
      ),
    );
  }
}
