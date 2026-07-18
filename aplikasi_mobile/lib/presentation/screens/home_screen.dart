import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../../core/theme/app_theme.dart';
import '../../data/models/app_user.dart';
import '../../data/models/security_dashboard.dart';
import '../../data/services/api_client.dart';
import '../../data/services/security_service.dart';
import 'late_student_scan_screen.dart';
import 'lateness_history_screen.dart';
import 'profile_screen.dart';

class SecurityHomeScreen extends StatefulWidget {
  const SecurityHomeScreen({required this.user, super.key});

  final AppUser user;

  @override
  State<SecurityHomeScreen> createState() => _SecurityHomeScreenState();
}

class _SecurityHomeScreenState extends State<SecurityHomeScreen> {
  int _selectedIndex = 0;
  int _dashboardRevision = 0;

  void _selectTab(int index) {
    setState(() {
      _selectedIndex = index;
      if (index == 0) _dashboardRevision++;
    });
  }

  @override
  Widget build(BuildContext context) {
    final pages = [
      SecurityDashboardView(
        key: ValueKey(_dashboardRevision),
        user: widget.user,
        onOpenScanner: () => _selectTab(1),
      ),
      LateStudentScanScreen(
        isActive: _selectedIndex == 1,
        onRecorded: () => setState(() => _dashboardRevision++),
      ),
      ProfileScreen(user: widget.user),
    ];

    return Scaffold(
      body: IndexedStack(index: _selectedIndex, children: pages),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _selectedIndex,
        onDestinationSelected: _selectTab,
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.dashboard_outlined),
            selectedIcon: Icon(Icons.dashboard_rounded),
            label: 'Dashboard',
          ),
          NavigationDestination(
            icon: Icon(Icons.qr_code_scanner_outlined),
            selectedIcon: Icon(Icons.qr_code_scanner_rounded),
            label: 'Scan',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline),
            selectedIcon: Icon(Icons.person_rounded),
            label: 'Profil',
          ),
        ],
      ),
    );
  }
}

class SecurityDashboardView extends StatefulWidget {
  const SecurityDashboardView({
    required this.user,
    required this.onOpenScanner,
    super.key,
  });

  final AppUser user;
  final VoidCallback onOpenScanner;

  @override
  State<SecurityDashboardView> createState() => _SecurityDashboardViewState();
}

class _SecurityDashboardViewState extends State<SecurityDashboardView> {
  final _service = SecurityService();
  late Future<SecurityDashboard> _dashboard;

  @override
  void initState() {
    super.initState();
    _dashboard = _service.getDashboard();
  }

  Future<void> _refresh() async {
    final request = _service.getDashboard();
    setState(() => _dashboard = request);
    await request;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Operasional Security'),
            Text(
              'SMK Telkom Lampung',
              style: TextStyle(
                color: Color(0xFF64748B),
                fontSize: 12,
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            tooltip: 'Muat ulang',
            onPressed: _refresh,
            icon: const Icon(Icons.refresh_rounded),
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _refresh,
        child: FutureBuilder<SecurityDashboard>(
          future: _dashboard,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const _DashboardLoading();
            }
            if (snapshot.hasError) {
              final message = snapshot.error is ApiException
                  ? (snapshot.error! as ApiException).message
                  : 'Data dashboard belum dapat dimuat.';
              return _DashboardError(message: message, onRetry: _refresh);
            }

            final dashboard = snapshot.data!;
            return ListView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.fromLTRB(20, 12, 20, 28),
              children: [
                Text(
                  'Selamat bertugas, ${widget.user.name}',
                  style: Theme.of(context).textTheme.titleLarge,
                ),
                const SizedBox(height: 4),
                Text(
                  DateFormat(
                    'EEEE, d MMMM yyyy',
                    'id_ID',
                  ).format(DateTime.now()),
                  style: const TextStyle(color: Color(0xFF64748B)),
                ),
                const SizedBox(height: 20),
                Row(
                  children: [
                    Expanded(
                      child: _StatTile(
                        label: 'Terlambat',
                        value: dashboard.lateToday,
                        icon: Icons.schedule_rounded,
                        color: AppTheme.red,
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: _StatTile(
                        label: 'Di luar',
                        value: dashboard.studentsOutside,
                        icon: Icons.directions_walk_rounded,
                        color: const Color(0xFFD97706),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: _StatTile(
                        label: 'Verifikasi',
                        value: dashboard.verifiedToday,
                        icon: Icons.verified_outlined,
                        color: const Color(0xFF047857),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 20),
                Material(
                  color: AppTheme.ink,
                  borderRadius: BorderRadius.circular(8),
                  child: InkWell(
                    onTap: widget.onOpenScanner,
                    borderRadius: BorderRadius.circular(8),
                    child: const Padding(
                      padding: EdgeInsets.all(18),
                      child: Row(
                        children: [
                          Icon(
                            Icons.qr_code_scanner_rounded,
                            color: Colors.white,
                            size: 34,
                          ),
                          SizedBox(width: 16),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Data Siswa Terlambat',
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 16,
                                    fontWeight: FontWeight.w800,
                                  ),
                                ),
                                SizedBox(height: 3),
                                Text(
                                  'Scan QR siswa atau cari berdasarkan NIS.',
                                  style: TextStyle(
                                    color: Color(0xFFCBD5E1),
                                    fontSize: 12,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          Icon(
                            Icons.arrow_forward_rounded,
                            color: Colors.white,
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 28),
                Row(
                  children: [
                    Expanded(
                      child: Text(
                        'Keterlambatan Terbaru',
                        style: Theme.of(context).textTheme.titleMedium,
                      ),
                    ),
                    TextButton(
                      onPressed: () => Navigator.of(context).push(
                        MaterialPageRoute<void>(
                          builder: (_) => const LatenessHistoryScreen(),
                        ),
                      ),
                      child: const Text('Lihat semua'),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                if (dashboard.recentLateness.isEmpty)
                  const _EmptyRecent()
                else
                  ...dashboard.recentLateness.map(
                    (record) => ListTile(
                      contentPadding: const EdgeInsets.symmetric(horizontal: 4),
                      leading: CircleAvatar(
                        backgroundColor: const Color(0xFFFFEBEC),
                        child: Text(
                          record.student.name.isEmpty
                              ? '?'
                              : record.student.name[0].toUpperCase(),
                          style: const TextStyle(
                            color: AppTheme.red,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                      title: Text(
                        record.student.name,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      subtitle: Text(
                        '${record.student.className} • ${record.reason}',
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      trailing: Text(
                        DateFormat('HH:mm').format(record.recordedAt.toLocal()),
                        style: const TextStyle(
                          color: Color(0xFF64748B),
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ),
              ],
            );
          },
        ),
      ),
    );
  }
}

class _StatTile extends StatelessWidget {
  const _StatTile({
    required this.label,
    required this.value,
    required this.icon,
    required this.color,
  });

  final String label;
  final int value;
  final IconData icon;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Container(
      constraints: const BoxConstraints(minHeight: 106),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 22),
          const Spacer(),
          Text(
            '$value',
            style: const TextStyle(
              color: AppTheme.ink,
              fontSize: 22,
              fontWeight: FontWeight.w900,
            ),
          ),
          Text(
            label,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(color: Color(0xFF64748B), fontSize: 11),
          ),
        ],
      ),
    );
  }
}

class _DashboardLoading extends StatelessWidget {
  const _DashboardLoading();

  @override
  Widget build(BuildContext context) {
    return ListView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.all(24),
      children: const [
        SizedBox(height: 150),
        Center(child: CircularProgressIndicator()),
      ],
    );
  }
}

class _DashboardError extends StatelessWidget {
  const _DashboardError({required this.message, required this.onRetry});

  final String message;
  final Future<void> Function() onRetry;

  @override
  Widget build(BuildContext context) {
    return ListView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.all(24),
      children: [
        const SizedBox(height: 100),
        const Icon(Icons.cloud_off_outlined, size: 48, color: AppTheme.red),
        const SizedBox(height: 16),
        Text(message, textAlign: TextAlign.center),
        const SizedBox(height: 20),
        FilledButton.icon(
          onPressed: onRetry,
          icon: const Icon(Icons.refresh_rounded),
          label: const Text('Coba lagi'),
        ),
      ],
    );
  }
}

class _EmptyRecent extends StatelessWidget {
  const _EmptyRecent();

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: const Row(
        children: [
          Icon(Icons.check_circle_outline, color: Color(0xFF047857)),
          SizedBox(width: 12),
          Expanded(
            child: Text('Belum ada siswa yang didata terlambat hari ini.'),
          ),
        ],
      ),
    );
  }
}
