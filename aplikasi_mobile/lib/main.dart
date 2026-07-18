import 'package:flutter/material.dart';
import 'package:intl/date_symbol_data_local.dart';

import 'core/theme/app_theme.dart';
import 'presentation/screens/splash_screen.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('id_ID');
  runApp(const SisfoMobileApp());
}

class SisfoMobileApp extends StatelessWidget {
  const SisfoMobileApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'SISFO SMK Telkom Lampung',
      theme: AppTheme.light,
      home: const SplashScreen(),
    );
  }
}
