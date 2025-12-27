import 'package:flutter/material.dart';
import 'profile_screen.dart';

class HomeScreen extends StatefulWidget {
  @override
  _HomeScreenState createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _selectedIndex = 0;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF8F9FA), // Background sangat terang
      body: Stack(
        children: [
          // 1. Background Header Gradiasi
          Container(
            height: 250,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [Color(0xFFC62828), Color(0xFFE53935)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(40),
                bottomRight: Radius.circular(40),
              ),
            ),
          ),

          SafeArea(
            child: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // 2. Profil Header
                  Padding(
                    padding: const EdgeInsets.all(25.0),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              "Halo, Budi Santoso",
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 22,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            Text(
                              "XII Teknik Komputer Jaringan 1",
                              style: TextStyle(
                                color: Colors.white70,
                                fontSize: 14,
                              ),
                            ),
                          ],
                        ),
                        GestureDetector(
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => ProfileScreen(),
                              ),
                            );
                          },
                          child: CircleAvatar(
                            radius: 30,
                            backgroundColor: Colors.white.withOpacity(0.3),
                            child: Icon(
                              Icons.person,
                              color: Colors.white,
                              size: 35,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),

                  // 3. Ringkasan Kehadiran (Card Melayang)
                  Container(
                    margin: EdgeInsets.symmetric(horizontal: 25),
                    padding: EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(25),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.05),
                          blurRadius: 20,
                          offset: Offset(0, 10),
                        ),
                      ],
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: [
                        _buildStatItem(
                          "Hadir",
                          "98%",
                          Icons.check_circle_outline,
                          Colors.green,
                        ),
                        _buildStatItem(
                          "Izin",
                          "2",
                          Icons.info_outline,
                          Colors.orange,
                        ),
                        _buildStatItem(
                          "Alpha",
                          "0",
                          Icons.cancel_outlined,
                          Colors.red,
                        ),
                      ],
                    ),
                  ),

                  SizedBox(height: 30),

                  // 4. Grid Menu
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 25),
                    child: Text(
                      "Layanan Akademik",
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: Colors.black87,
                      ),
                    ),
                  ),
                  SizedBox(height: 15),
                  GridView.count(
                    shrinkWrap: true,
                    physics: NeverScrollableScrollPhysics(),
                    crossAxisCount: 4,
                    padding: EdgeInsets.symmetric(horizontal: 20),
                    children: [
                      _buildMenuItem(
                        "Jadwal",
                        Icons.calendar_today,
                        Colors.blue,
                      ),
                      _buildMenuItem(
                        "Nilai",
                        Icons.assignment_turned_in,
                        Colors.orange,
                      ),
                      _buildMenuItem("Absensi", Icons.fingerprint, Colors.teal),
                      _buildMenuItem(
                        "Bayar",
                        Icons.account_balance_wallet,
                        Colors.purple,
                      ),
                      _buildMenuItem(
                        "E-Perpus",
                        Icons.menu_book,
                        Colors.indigo,
                      ),
                      _buildMenuItem(
                        "E-Learning",
                        Icons.laptop_mac,
                        Colors.brown,
                      ),
                      _buildMenuItem("Ospek", Icons.people, Colors.pink),
                      _buildMenuItem(
                        "Lainnya",
                        Icons.grid_view_rounded,
                        Colors.grey,
                      ),
                    ],
                  ),

                  SizedBox(height: 30),

                  // 5. Pengumuman Terbaru
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 25),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          "Pengumuman",
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        Text(
                          "Lihat Semua",
                          style: TextStyle(color: Color(0xFFC62828)),
                        ),
                      ],
                    ),
                  ),
                  _buildAnnouncementCard(),
                  SizedBox(height: 100), // Memberi ruang untuk Bottom Nav
                ],
              ),
            ),
          ),
        ],
      ),

      // 6. Floating Bottom Navigation Bar
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
      floatingActionButton: Container(
        height: 65,
        margin: EdgeInsets.symmetric(horizontal: 20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(30),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 20,
              offset: Offset(0, 5),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(30),
          child: BottomNavigationBar(
            currentIndex: _selectedIndex,
            onTap: (index) {
              if (index == 3) {
                // Angka 3 adalah urutan ikon Profile (0,1,2,3)
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => ProfileScreen()),
                );
              } else {
                setState(() => _selectedIndex = index);
              }
            },
            showSelectedLabels: false,
            showUnselectedLabels: false,
            selectedItemColor: Color(0xFFC62828),
            unselectedItemColor: Colors.grey[400],
            type: BottomNavigationBarType.fixed,
            backgroundColor: Colors.white,
            items: [
              BottomNavigationBarItem(
                icon: Icon(Icons.home_rounded, size: 30),
                label: "Home",
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.notifications_none_rounded, size: 30),
                label: "Notif",
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.chat_bubble_outline_rounded, size: 28),
                label: "Pesan",
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.person_outline_rounded, size: 30),
                label: "Profile",
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Widget Helper untuk Statistik
  Widget _buildStatItem(
    String label,
    String value,
    IconData icon,
    Color color,
  ) {
    return Column(
      children: [
        Icon(icon, color: color, size: 28),
        SizedBox(height: 8),
        Text(
          value,
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
        ),
        Text(label, style: TextStyle(color: Colors.grey, fontSize: 12)),
      ],
    );
  }

  // Widget Helper untuk Menu
  Widget _buildMenuItem(String title, IconData icon, Color color) {
    return Column(
      children: [
        Container(
          padding: EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(15),
          ),
          child: Icon(icon, color: color, size: 28),
        ),
        SizedBox(height: 8),
        Text(
          title,
          style: TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w600,
            color: Colors.black87,
          ),
          textAlign: TextAlign.center,
        ),
      ],
    );
  }

  // Widget Helper untuk Pengumuman
  Widget _buildAnnouncementCard() {
    return Container(
      margin: EdgeInsets.all(25),
      padding: EdgeInsets.all(15),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.withOpacity(0.1)),
      ),
      child: Row(
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: Colors.red[50],
              borderRadius: BorderRadius.circular(15),
            ),
            child: Icon(Icons.campaign, color: Color(0xFFC62828), size: 40),
          ),
          SizedBox(width: 15),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  "Ujian Akhir Semester",
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                ),
                SizedBox(height: 5),
                Text(
                  "Pelaksanaan UAS akan dimulai pada tanggal 2 Januari 2025...",
                  style: TextStyle(color: Colors.grey, fontSize: 13),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
