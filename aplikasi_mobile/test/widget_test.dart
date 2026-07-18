import 'package:aplikasi_mobile/core/theme/app_theme.dart';
import 'package:aplikasi_mobile/data/models/app_user.dart';
import 'package:aplikasi_mobile/data/models/student.dart';
import 'package:aplikasi_mobile/presentation/screens/login_screen.dart';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('login menampilkan form akun SISFO', (tester) async {
    await tester.pumpWidget(
      MaterialApp(theme: AppTheme.light, home: const LoginScreen()),
    );

    expect(find.text('Masuk ke SISFO'), findsOneWidget);
    expect(find.text('Email sekolah'), findsOneWidget);
    expect(find.text('Password'), findsOneWidget);
    expect(find.text('Masuk'), findsOneWidget);
  });

  test('role Security dikenali tanpa bergantung kapitalisasi', () {
    const user = AppUser(
      id: 1,
      name: 'Petugas',
      email: 'security@example.test',
      roles: ['security'],
    );

    expect(user.isSecurity, isTrue);
  });

  test('model siswa membaca status keterlambatan hari ini', () {
    final student = Student.fromJson({
      'id': 10,
      'nis': '12345',
      'name': 'Andi',
      'class_name': 'X TKJ 1',
      'already_late_today': true,
    });

    expect(student.name, 'Andi');
    expect(student.alreadyLateToday, isTrue);
  });
}
