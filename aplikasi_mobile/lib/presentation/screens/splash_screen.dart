import 'dart:async';
import 'package:flutter/material.dart';
import 'login_screen.dart';

class SplashScreen extends StatefulWidget {
  @override
  _SplashScreenState createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  double _opacity = 0.0;

  @override
  void initState() {
    super.initState();
    // Memulai animasi setelah delay singkat
    Timer(Duration(milliseconds: 500), () {
      setState(() {
        _opacity = 1.0;
      });
    });

    // Pindah ke halaman Login/Home setelah 3 detik
    Timer(Duration(seconds: 4), () {
      // Navigasi ke halaman berikutnya (Ganti ke route tujuan Anda)
      // Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) => LoginScreen()));
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => LoginScreen()),
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        width: double.infinity,
        height: double.infinity,
        // Gradasi modern standar industri (Soft Gradient)
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Color(0xFFFFFFFF), // Putih Bersih
              Color(0xFFFDEEEE), // Merah sangat muda (Soft Pinkish)
              Color(0xFFF8D7D7), // Soft Red Mist
            ],
          ),
        ),
        child: AnimatedOpacity(
          duration: Duration(seconds: 2),
          opacity: _opacity,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Efek Soft UI pada Container Logo
              Container(
                padding: EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.8),
                  shape: BoxShape.circle,
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.05),
                      blurRadius: 20,
                      spreadRadius: 5,
                    ),
                  ],
                ),
                child: Image.asset(
                  'assets/images/logo_telkom.png', // Pastikan path benar
                  height: 120,
                ),
              ),
              SizedBox(height: 24),
              // Teks dengan gaya Modern & Minimalis
              Text(
                "SMK TELKOM",
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 2,
                  color: Color(0xFFC62828), // Red Telkom
                ),
              ),
              Text(
                "LAMPUNG",
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w300,
                  letterSpacing: 8,
                  color: Colors.grey[700],
                ),
              ),
              SizedBox(height: 50),
              // Loading Indicator yang halus
              CircularProgressIndicator(
                strokeWidth: 2,
                valueColor: AlwaysStoppedAnimation<Color>(Color(0xFFC62828)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
