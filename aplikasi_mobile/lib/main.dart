import 'package:flutter/material.dart';
import 'presentation/screens/splash_screen.dart'; // Import file yang baru dibuat

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'SIM SMK Telkom Lampung',
      theme: ThemeData(
        primarySwatch: Colors.red,
        fontFamily: 'Poppins', // Opsional: Jika Anda menggunakan font custom
      ),
      home: SplashScreen(), // Set halaman awal ke SplashScreen
    );
  }
}
