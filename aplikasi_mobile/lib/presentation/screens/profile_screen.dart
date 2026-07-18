import 'package:flutter/material.dart';

import '../../core/theme/app_theme.dart';
import '../../data/models/app_user.dart';
import '../../data/services/auth_service.dart';
import 'login_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({required this.user, super.key});

  final AppUser user;

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  bool _loggingOut = false;

  Future<void> _logout() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Keluar dari aplikasi?'),
        content: const Text(
          'Anda perlu masuk kembali untuk menggunakan layanan Security.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Keluar'),
          ),
        ],
      ),
    );
    if (confirmed != true || !mounted) return;

    setState(() => _loggingOut = true);
    await AuthService.instance.logout();
    if (!mounted) return;
    Navigator.of(context).pushAndRemoveUntil(
      MaterialPageRoute<void>(builder: (_) => const LoginScreen()),
      (_) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Profil Petugas')),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          const SizedBox(height: 8),
          CircleAvatar(
            radius: 42,
            backgroundColor: const Color(0xFFFFEBEC),
            child: Text(
              widget.user.name.isEmpty
                  ? '?'
                  : widget.user.name[0].toUpperCase(),
              style: const TextStyle(
                color: AppTheme.red,
                fontSize: 32,
                fontWeight: FontWeight.w900,
              ),
            ),
          ),
          const SizedBox(height: 16),
          Text(
            widget.user.name,
            textAlign: TextAlign.center,
            style: Theme.of(context).textTheme.titleLarge,
          ),
          const SizedBox(height: 4),
          Text(
            widget.user.email,
            textAlign: TextAlign.center,
            style: const TextStyle(color: Color(0xFF64748B)),
          ),
          const SizedBox(height: 24),
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: const Color(0xFFE2E8F0)),
            ),
            child: Column(
              children: [
                _ProfileItem(
                  icon: Icons.badge_outlined,
                  label: 'Role aktif',
                  value: widget.user.primaryRole,
                ),
                const Divider(height: 1),
                const _ProfileItem(
                  icon: Icons.security_outlined,
                  label: 'Akses aplikasi',
                  value: 'Operasional Security',
                ),
                const Divider(height: 1),
                const _ProfileItem(
                  icon: Icons.lock_outline,
                  label: 'Penyimpanan sesi',
                  value: 'Terenkripsi di perangkat',
                ),
              ],
            ),
          ),
          const SizedBox(height: 28),
          OutlinedButton.icon(
            onPressed: _loggingOut ? null : _logout,
            icon: _loggingOut
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(strokeWidth: 2),
                  )
                : const Icon(Icons.logout_rounded),
            label: Text(_loggingOut ? 'Mengakhiri sesi...' : 'Keluar'),
          ),
        ],
      ),
    );
  }
}

class _ProfileItem extends StatelessWidget {
  const _ProfileItem({
    required this.icon,
    required this.label,
    required this.value,
  });

  final IconData icon;
  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: Icon(icon, color: AppTheme.red),
      title: Text(label),
      subtitle: Text(value),
    );
  }
}
