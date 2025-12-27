import 'package:flutter/material.dart';

class ProfileScreen extends StatefulWidget {
  @override
  _ProfileScreenState createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  // Controller untuk input (Contoh untuk update data)
  final TextEditingController _nameController = TextEditingController(
    text: "Budi Santoso",
  );
  final TextEditingController _phoneController = TextEditingController(
    text: "081234567890",
  );
  final TextEditingController _addressController = TextEditingController(
    text: "Jl. Teuku Umar No. 1, Lampung",
  );

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF8F9FA),
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.transparent,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new, color: Colors.black),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          "Profil Siswa",
          style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 25.0),
          child: Column(
            children: [
              SizedBox(height: 20),

              // 1. Foto Profil dengan Tombol Edit
              Center(
                child: Stack(
                  children: [
                    Container(
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 5),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.1),
                            blurRadius: 20,
                            offset: Offset(0, 10),
                          ),
                        ],
                      ),
                      child: CircleAvatar(
                        radius: 60,
                        backgroundColor: Color(0xFFFDEEEE),
                        child: Icon(
                          Icons.person,
                          size: 70,
                          color: Color(0xFFC62828),
                        ),
                      ),
                    ),
                    Positioned(
                      bottom: 0,
                      right: 0,
                      child: Container(
                        padding: EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: Color(0xFFC62828),
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white, width: 2),
                        ),
                        child: Icon(
                          Icons.camera_alt,
                          color: Colors.white,
                          size: 20,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              SizedBox(height: 30),

              // 2. Data Akademik (Read Only Card)
              Container(
                width: double.infinity,
                padding: EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.02),
                      blurRadius: 10,
                    ),
                  ],
                ),
                child: Column(
                  children: [
                    _buildReadOnlyData("NISN", "0012345678"),
                    Divider(height: 30),
                    _buildReadOnlyData(
                      "Program Keahlian",
                      "Teknik Komputer Jaringan",
                    ),
                    Divider(height: 30),
                    _buildReadOnlyData("Status", "Siswa Aktif"),
                  ],
                ),
              ),
              SizedBox(height: 25),

              // 3. Form Update Data (Editable)
              Align(
                alignment: Alignment.centerLeft,
                child: Text(
                  "Data Pribadi",
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
              ),
              SizedBox(height: 15),
              _buildEditableField(
                "Nama Lengkap",
                _nameController,
                Icons.person_outline,
              ),
              SizedBox(height: 15),
              _buildEditableField(
                "Nomor WhatsApp",
                _phoneController,
                Icons.phone_android,
              ),
              SizedBox(height: 15),
              _buildEditableField(
                "Alamat",
                _addressController,
                Icons.location_on_outlined,
                maxLines: 3,
              ),

              SizedBox(height: 40),

              // 4. Tombol Simpan
              Container(
                width: double.infinity,
                height: 55,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(15),
                  gradient: LinearGradient(
                    colors: [Color(0xFFC62828), Color(0xFFE53935)],
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: Color(0xFFC62828).withOpacity(0.3),
                      blurRadius: 15,
                      offset: Offset(0, 8),
                    ),
                  ],
                ),
                child: ElevatedButton(
                  onPressed: () {
                    // Logika simpan data di sini
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text("Profil berhasil diperbarui!")),
                    );
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.transparent,
                    shadowColor: Colors.transparent,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(15),
                    ),
                  ),
                  child: Text(
                    "SIMPAN PERUBAHAN",
                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
              SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  // Widget Helper untuk Data Akademik
  Widget _buildReadOnlyData(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: TextStyle(color: Colors.grey[600])),
        Text(
          value,
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.black87),
        ),
      ],
    );
  }

  // Widget Helper untuk Input Update
  Widget _buildEditableField(
    String label,
    TextEditingController controller,
    IconData icon, {
    int maxLines = 1,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: TextStyle(fontSize: 14, color: Colors.grey[700])),
        SizedBox(height: 8),
        Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(15),
            border: Border.all(color: Colors.grey.withOpacity(0.1)),
          ),
          child: TextField(
            controller: controller,
            maxLines: maxLines,
            decoration: InputDecoration(
              prefixIcon: Icon(icon, color: Color(0xFFC62828)),
              border: InputBorder.none,
              contentPadding: EdgeInsets.all(15),
            ),
          ),
        ),
      ],
    );
  }
}
