<?php

namespace Database\Seeders;

use App\Services\PrakerinRombelImportService;
use Illuminate\Database\Seeder;

class PrakerinRombelPklSeeder extends Seeder
{
    /**
     * Run the database seeds for SK Pembimbing Siswa PKL Kelas XII TA. 2026/2027.
     */
    public function run(): void
    {
        $rawCsv = <<<'CSV'
1,"Ahmad Iksan, M.Kom",Arda Meysya,553241026,XII TKJ 1,BPS Pesawaran,9
1,"Ahmad Iksan, M.Kom",Gladys Putri Kinara,553241072,XII TKJ 1,Telkom Witel lampung,9
1,"Ahmad Iksan, M.Kom",Rachel Adhareza,553241137,XII TKJ 1,BPS Pesawaran,9
1,"Ahmad Iksan, M.Kom",Tubagus Agung M,553241171,XII TKJ 1,Dinas Kominfo Pesawaran,9
1,"Ahmad Iksan, M.Kom",Wiwik Sagita,553241174,XII TKJ 1,Telkom Witel lampung,9
1,"Ahmad Iksan, M.Kom",Fajar Aris Saputra,553241056,XII TKJ 2,Telkom Akses Kedaton,9
1,"Ahmad Iksan, M.Kom",Fani Aulia Azzahra,553241058,XII TKJ 2,Telkom Akses Kedaton,9
1,"Ahmad Iksan, M.Kom",Ferdian Wisnu Alzidan,553241067,XII TKJ 2,Telkom Akses Kedaton,9
1,"Ahmad Iksan, M.Kom",Leonnard Yuri Taupiqqurrahman,553241095,XII TKJ 2,Telkom Akses Kedaton,9
2,"Aji Sakti Kurniawan, S.Kom",Afgan Zahari,553241005,XII ANIMASI,Ventura Studios,4
2,"Aji Sakti Kurniawan, S.Kom",Aira Fahrizki,553241181,XII ANIMASI,Ventura Studios,4
2,"Aji Sakti Kurniawan, S.Kom",Alira Nuur Ramadhani,553241021,XII ANIMASI,Ventura Studios,4
2,"Aji Sakti Kurniawan, S.Kom",Ar Rafi Leroy Aira M,553241184,XII ANIMASI,Ventura Studios,4
3,"Anang Esa Sulistiawan, S.Kom",Aira Al Fatunnisa,553241013,XII TJAT,PLN ULP Pringsewu,4
3,"Anang Esa Sulistiawan, S.Kom",Aurel Rizkia Anantasya,553241032,XII TJAT,PLN ULP Pringsewu,4
3,"Anang Esa Sulistiawan, S.Kom",Muhammad Nur Zaki,553241183,XII TJAT,PLN ULP Pringsewu,4
3,"Anang Esa Sulistiawan, S.Kom",Savana Gusti Ayunda,553241159,XII TJAT,PLN ULP Pringsewu,4
4,"Arohman, S.Kom",M Atha Haikal Lazuardi,553241097,XII TJAT,Telkom Akses Kedaton,2
4,"Arohman, S.Kom",Muhammad Iqbal Abdillah,553241116,XII TJAT,Telkom Akses Kedaton,2
5,"Astri Damayanti, S.Pd",Jihan Meysun,553241087,XII TKJ 1,Telkom Akses Kedaton,6
5,"Astri Damayanti, S.Pd",Muchlas Sidik,553241185,XII TKJ 1,Telkom Akses Kedaton,6
5,"Astri Damayanti, S.Pd",Naufal Hanif,553241127,XII TKJ 2,Griyacom,6
5,"Astri Damayanti, S.Pd",Rafi Arta Nugraha,553241140,XII TKJ 2,PT. KAI (Divre IV Tanjungkarang),6
5,"Astri Damayanti, S.Pd",Akbar Rahmadani Rahman,553241014,XII RPL 2,Polda lampung,6
5,"Astri Damayanti, S.Pd",Bastian Ilham Farizqi,553241038,XII RPL 2,Polda lampung,6
6,"Azhar Mustofa, S.Kom",Chika Aulia Putri,553241043,XII TKJ 1,BPN Pringsewu,7
6,"Azhar Mustofa, S.Kom",Rosalia Putri,553241157,XII TKJ 1,Badan Pusat Statistik Pringsewu,7
6,"Azhar Mustofa, S.Kom",Silviyana,553241163,XII TKJ 1,BPN Pringsewu,7
6,"Azhar Mustofa, S.Kom",Wulan Nur Septi Ambar Ramadhani,553241175,XII TKJ 1,Kantor Pengadilan Agama Pringsewu,7
6,"Azhar Mustofa, S.Kom",Farhan Akbar Rahim,553241060,XII TKJ 2,Kantor Kecamatan GadingRejo,7
6,"Azhar Mustofa, S.Kom",Iksan Kurnia,553241077,XII TKJ 2,Kantor Kecamatan GadingRejo,7
6,"Azhar Mustofa, S.Kom",Imam Thantowy Al Bashary Ahmad,553241079,XII TKJ 2,Kantor Kecamatan GadingRejo,7
7,"Budi Safta Nugraha, S.Kom",Ahmad Zaidani,553241012,XII RPL 1,Universitas Teknokrat Indonesia,9
7,"Budi Safta Nugraha, S.Kom",Dzaky Farhan Orlando,553241051,XII RPL 1,Universitas Teknokrat Indonesia,9
7,"Budi Safta Nugraha, S.Kom",Fatah Khairurrizqy Budiansyah,553241063,XII RPL 1,Universitas Teknokrat Indonesia,9
7,"Budi Safta Nugraha, S.Kom",Firza Alfalah,553241069,XII RPL 1,Universitas Teknokrat Indonesia,9
7,"Budi Safta Nugraha, S.Kom",Juan Raffa Davidenco,553241089,XII RPL 1,Universitas Teknokrat Indonesia,9
7,"Budi Safta Nugraha, S.Kom",Yoel Arvino,553241176,XII RPL 1,Universitas Teknokrat Indonesia,9
7,"Budi Safta Nugraha, S.Kom",Bening Prasasti,553241040,XII RPL 2,Telkom Akses Kedaton,9
7,"Budi Safta Nugraha, S.Kom",Khoirun Nisya,553241091,XII RPL 2,Telkom Akses Kedaton,9
7,"Budi Safta Nugraha, S.Kom",M.Ridho Suryatama,553241102,XII RPL 2,Diskominfo Provinsi Lampung,9
8,"Dany Rahmatullah, S.Kom",Arien Putri Nuraini,553241028,XII ANIMASI,Ventura Studios,9
8,"Dany Rahmatullah, S.Kom",Aulia Zulfatus Shoraya,553241031,XII ANIMASI,PT Educa Sisfomedia Indonesia (Gamelab.ID),9
8,"Dany Rahmatullah, S.Kom",Fitria Sifa,553241070,XII ANIMASI,PT Educa Sisfomedia Indonesia (Gamelab.ID),9
8,"Dany Rahmatullah, S.Kom",Indri Zaskia Bella,553241081,XII ANIMASI,PT Educa Sisfomedia Indonesia (Gamelab.ID),9
8,"Dany Rahmatullah, S.Kom",Muhammad Farel Endarlen,553241114,XII ANIMASI,PT. Briliant think center,9
8,"Dany Rahmatullah, S.Kom",Nabila Luthfiana Ulfa,553241122,XII ANIMASI,PT Educa Sisfomedia Indonesia (Gamelab.ID),9
8,"Dany Rahmatullah, S.Kom",Qothrunnada Fadhilah Latief,553241136,XII ANIMASI,Ventura Studios,9
8,"Dany Rahmatullah, S.Kom",Reyga Sabrina,553241149,XII ANIMASI,Ventura Studios,9
8,"Dany Rahmatullah, S.Kom",Rini Asri Tanjung,553241154,XII ANIMASI,Dinas Kominfo Pesawaran,9
9,"Dita Safitri, S.Pd",Ardiyansah Kurniawan,553241027,XII RPL 1,Kandatel Metro,4
9,"Dita Safitri, S.Pd",Farid Hakim Maulana,553241062,XII RPL 1,Kandatel Metro,4
9,"Dita Safitri, S.Pd",Febian Muhammad Alfa,553241064,XII RPL 2,Badan Kepegawaian Daerah Prov Lampung,4
9,"Dita Safitri, S.Pd",Taqiyudin Abdulloh,553241168,XII RPL 2,Badan Kepegawaian Daerah Prov Lampung,4
10,"Erwin Romel, S.Kom",Wisnu Tri Satria,553241173,XII RPL 1,PT Educa Sisfomedia Indonesia (Gamelab.ID),8
10,"Erwin Romel, S.Kom",Ilham Syahmudra,553241078,XII RPL 2,PLN Icon Plus Bandar Lampung (ISP),8
10,"Erwin Romel, S.Kom",Indra Prawangsa,553241080,XII RPL 2,PT Educa Sisfomedia Indonesia (Gamelab.ID),8
10,"Erwin Romel, S.Kom",Muazham Hafidz Umar,553241107,XII RPL 2,PT Educa Sisfomedia Indonesia (Gamelab.ID),8
10,"Erwin Romel, S.Kom",Muhammad Ibnu Mudzakir,553241115,XII RPL 2,PLN Icon Plus Bandar Lampung (ISP),8
10,"Erwin Romel, S.Kom",Muhammad Zidan Al Fatah,553241121,XII RPL 2,PLN Icon Plus Bandar Lampung (ISP),8
10,"Erwin Romel, S.Kom",Dhean Oktaviansyah,553241048,XII RPL 1,PT Educa Sisfomedia Indonesia (Gamelab.ID),8
10,"Erwin Romel, S.Kom",Muhammad Amirul Rochsas,553241112,XII RPL 1,PT Educa Sisfomedia Indonesia (Gamelab.ID),8
11,"Fatriade Saputra, S.Kom",Mario Saputra,553241104,XII TKJ 1,Dinas Kominfo Pesawaran,9
11,"Fatriade Saputra, S.Kom",Muhammad Rusryan Janadi,553241120,XII TKJ 1,Dinas Kominfo Pesawaran,9
11,"Fatriade Saputra, S.Kom",Rafly Aditya Pratama,553241141,XII TKJ 1,Dinas Kominfo Pesawaran,9
11,"Fatriade Saputra, S.Kom",Refa Pratama,553241146,XII TKJ 1,Dinas Kominfo Pesawaran,9
11,"Fatriade Saputra, S.Kom",Taufik Rahman,553241169,XII TKJ 1,Dinas Kominfo Pesawaran,9
11,"Fatriade Saputra, S.Kom",Alif Fudhail Syafiq,553241020,XII TKJ 2,Telkom Akses Kedaton,9
11,"Fatriade Saputra, S.Kom",Cheycia Aulia,553241041,XII TKJ 2,Telkom Akses Kedaton,9
11,"Fatriade Saputra, S.Kom",Febrina Putri Amalia,553241065,XII TKJ 2,Telkom Akses Kedaton,9
11,"Fatriade Saputra, S.Kom",Fina Febriyanti,553241068,XII TKJ 2,Telkom Akses Kedaton,9
12,"Faalih Qowiy, S.Pd",Reza Oktafiyani,553241150,XII RPL 2,Telkom Witel Lampung,5
12,"Faalih Qowiy, S.Pd",Selvi Dea Rifa,553241160,XII RPL 2,Telkom Witel Lampung,5
12,"Faalih Qowiy, S.Pd",Suci Nuraidah,553241164,XII RPL 2,Telkom Witel Lampung,5
12,"Faalih Qowiy, S.Pd",Muhammad Rafly Pasiwaratu,553241117,XII TJAT,Telkom Akses Kedaton,5
12,"Faalih Qowiy, S.Pd",Nadika Raisyatama,553241124,XII TJAT,Telkom Akses Kedaton,5
13,"Hari Hartoyo, S.Pd",Egy Fitra Yudha,553241052,XII TKJ 1,Kandatel Pringsewu,3
13,"Hari Hartoyo, S.Pd",Raden Ahmad Sanni,553241138,XII TKJ 2,Dinas Kominfo Pesawaran,3
13,"Hari Hartoyo, S.Pd",Rifki Aditama Putra,553241153,XII TJAT,PT.ANJAS PRIMA TEKNIK (Makasar),3
14,"Hermawan Rijal Arrasy, S.Kom",Alya Ristia Oktaviani,553241022,XII TKJ 1,Kantor Pengadilan Agama Pringsewu,11
14,"Hermawan Rijal Arrasy, S.Kom",Dimas Prayoga,553241049,XII TKJ 1,ITBA DCC Pringsewu,11
14,"Hermawan Rijal Arrasy, S.Kom",Farhan Ahmad Alfarizi L,553241059,XII TKJ 1,Kantor Pengadilan Agama Pringsewu,11
14,"Hermawan Rijal Arrasy, S.Kom",Hanif Iffani,553241073,XII TKJ 1,Kantor Pengadilan Agama Pringsewu,11
14,"Hermawan Rijal Arrasy, S.Kom",Ridho Caisar Armando,553241152,XII TKJ 1,Kantor Pengadilan Agama Pringsewu,11
14,"Hermawan Rijal Arrasy, S.Kom",Romita Pratiswati,553241156,XII TKJ 1,Kantor Pengadilan Agama Pringsewu,11
14,"Hermawan Rijal Arrasy, S.Kom",Yuli Sazahra,553241177,XII TKJ 1,Kantor Pengadilan Agama Pringsewu,11
14,"Hermawan Rijal Arrasy, S.Kom",Zhaafirah,553241179,XII TKJ 1,Kantor Pengadilan Agama Pringsewu,11
14,"Hermawan Rijal Arrasy, S.Kom",Az Zhara Putri Altafiah,553241034,XII TKJ 2,Badan Pusat Statistik Pringsewu,11
14,"Hermawan Rijal Arrasy, S.Kom",Fadil Fajriansyah,553241055,XII TKJ 2,ITBA DCC Pringsewu,11
14,"Hermawan Rijal Arrasy, S.Kom",Khoirunisa,553241092,XII TKJ 2,Telkomsel Pringsewu,11
15,"Ihwan Hamid Huzain, S.Kom",Ahmad Axl Baldwin Bonaparte,553241007,XII RPL 1,IBI Darmajaya,9
15,"Ihwan Hamid Huzain, S.Kom",Akhdan Abrori,553241015,XII RPL 1,PT Prabutirta Jaya Lestari,9
15,"Ihwan Hamid Huzain, S.Kom",Aziza Azzahra,553241036,XII RPL 1,IBI Darmajaya,9
15,"Ihwan Hamid Huzain, S.Kom",Zean Tristyan,553241178,XII RPL 1,PT Prabutirta Jaya Lestari,9
15,"Ihwan Hamid Huzain, S.Kom",Ahmad Irsyad Alaudin,553241010,XII RPL 2,Diskominfo Provinsi Lampung,9
15,"Ihwan Hamid Huzain, S.Kom",Iqbal Brillian Taqwa,553241082,XII RPL 2,CV. Newus Technologi,9
15,"Ihwan Hamid Huzain, S.Kom",Kohri Catur Prasetyo,553241094,XII RPL 2,Diskominfo Provinsi Lampung,9
15,"Ihwan Hamid Huzain, S.Kom",M Vicri Al Musti,553241100,XII RPL 2,Diskominfo Provinsi Lampung,9
15,"Ihwan Hamid Huzain, S.Kom",Muhamad Rifky Abdullah,553241109,XII RPL 2,Diskominfo Provinsi Lampung,9
16,"Imam Suhendri, S.Kom",Aditama Yudistira,553241003,XII TJAT,Dinas Kominfo Pesawaran,10
16,"Imam Suhendri, S.Kom",Aditia Putra Rahmadani,553241004,XII TJAT,Telkom Akses Teluk Betung (Telkom Kedaton),10
16,"Imam Suhendri, S.Kom",Aflah Al Fatiha,553241006,XII TJAT,Polres Pringsewu,10
16,"Imam Suhendri, S.Kom",Aksa Ferdianto,553241016,XII TJAT,Telkom Akses Kedaton,10
16,"Imam Suhendri, S.Kom",Aldi Setiawan,553241017,XII TJAT,Telkom Akses Kedaton,10
16,"Imam Suhendri, S.Kom",Amry Aulia Antoni,553241023,XII TJAT,Telkom Akses Kedaton,10
16,"Imam Suhendri, S.Kom",Annauval Ghani Azhari,553241025,XII TJAT,Telkom Akses Kedaton,10
16,"Imam Suhendri, S.Kom",Dani Wahyu Saputra,553241044,XII TJAT,PT. MMS,10
16,"Imam Suhendri, S.Kom",Farid,553241061,XII TJAT,Dinas Kominfo Pesawaran,10
16,"Imam Suhendri, S.Kom",Rian Adli Gantari,553241151,XII TJAT,Dinas Kominfo Pesawaran,10
17,"Khafidh Febriansyah, S.Pd",Belva Hanasta Hakim,553241039,XII TJAT,Kandatel Metro,1
18,"Meliya Trisna Wahyuni, S.Pd",Nindya Putri Amelia,553241130,XII TKJ 1,Telkom Akses Kedaton,12
18,"Meliya Trisna Wahyuni, S.Pd",Nur Aisyah,553241131,XII TKJ 1,Telkom Akses Kedaton,12
18,"Meliya Trisna Wahyuni, S.Pd",Khusnul Khotimah,553241093,XII TKJ 2,Kandatel Pringsewu,12
18,"Meliya Trisna Wahyuni, S.Pd",Mega Puspitah,553241105,XII TKJ 2,Kandatel Pringsewu,12
18,"Meliya Trisna Wahyuni, S.Pd",Nuzul Aliftha Hasanah,553241132,XII RPL 2,Telkom Witel Lampung Kantor Enggal,12
18,"Meliya Trisna Wahyuni, S.Pd",Qirana Fikriyah,553241135,XII RPL 2,Telkom Witel Lampung Kantor Enggal,12
18,"Meliya Trisna Wahyuni, S.Pd",Rafa Farid Fadillah,553241139,XII RPL 2,PT Educa Sisfomedia Indonesia (Gamelab.ID),12
18,"Meliya Trisna Wahyuni, S.Pd",Zunanda Alqiendy Mukoffa,553241180,XII RPL 2,PT Educa Sisfomedia Indonesia (Gamelab.ID),12
18,"Meliya Trisna Wahyuni, S.Pd",Dzakwan Akmal Firdaus,553241050,XII TJAT,Telkom Akses Kedaton,12
18,"Meliya Trisna Wahyuni, S.Pd",Jonatan Giandri Valentino,553241088,XII TJAT,Telkom Akses Kedaton,12
18,"Meliya Trisna Wahyuni, S.Pd",Alfino Ananda Setyawan,553241019,XII ANIMASI,PT. Utama Santosa Lestari,12
18,"Meliya Trisna Wahyuni, S.Pd",Muhamad Farras Abiyyu,553241108,XII ANIMASI,Ventura Studios,12
19,"Muhammad Galih Febrian, S.Pd",Khensa Janitra Pratama,553241090,XII RPL 1,BPS Pesawaran,3
19,"Muhammad Galih Febrian, S.Pd",Vintra Naezma Wanggay,553241172,XII RPL 2,Kandatel Metro,3
19,"Muhammad Galih Febrian, S.Pd",Ika Aulia,553241075,XII ANIMASI,Kandatel Pringsewu,3
20,"Nur Cahyana Aminullah, S.Kom",Alfina Awalullaili Kurniawan,553241018,XII RPL 1,Universitas Malahayati,11
20,"Nur Cahyana Aminullah, S.Kom",Arjun Hafidz Meiansyah,553241029,XII RPL 1,BMTI Bandung,11
20,"Nur Cahyana Aminullah, S.Kom",Ayomi Enjang Utami,553241033,XII RPL 1,CV. Newus Technologi,11
20,"Nur Cahyana Aminullah, S.Kom",Azahra Salsabila,553241035,XII RPL 1,CV. Newus Technologi,11
20,"Nur Cahyana Aminullah, S.Kom",Cheysa Adelia Safitri,553241042,XII RPL 1,Universitas Malahayati,11
20,"Nur Cahyana Aminullah, S.Kom",Ika Safitri Oktavia,553241076,XII RPL 1,CV. Newus Technologi,11
20,"Nur Cahyana Aminullah, S.Kom",Muhammad Evander Alvaro,553241113,XII RPL 1,Telkom Akses Kedaton,11
20,"Nur Cahyana Aminullah, S.Kom",Syamil Cholid Atsani Marjuki,553241167,XII RPL 1,BMTI Bandung,11
20,"Nur Cahyana Aminullah, S.Kom",Lintang Mutiur Rahman,553241096,XII RPL 2,BMTI Bandung,11
20,"Nur Cahyana Aminullah, S.Kom",M Zalva Fedros Rellyan Aj,553241101,XII RPL 2,PT Educa Sisfomedia Indonesia (Gamelab.ID),11
20,"Nur Cahyana Aminullah, S.Kom",Muhammad Al Rasyid,553241111,XII RPL 2,BMTI Bandung,11
21,"Rendi Syaputra, S.Kom",Meutiyara Cesha Azzahra,553241106,XII RPL 2,Telkom Akses Kedaton,4
21,"Rendi Syaputra, S.Kom",M Iqbal Al Ghaits,553241099,XII RPL 2,Telkom Akses Kedaton,4
21,"Rendi Syaputra, S.Kom",Mardalena Kharisma Dila,553241103,XII RPL 2,Telkom Akses Kedaton,4
21,"Rendi Syaputra, S.Kom",Naila Naifsyah,553241125,XII RPL 2,Telkom Akses Kedaton,4
22,"Rizky Nurmansyah, S.Pd",Febrita Fadillah Mutia,553241066,XII RPL 1,IBI Darmajaya,6
22,"Rizky Nurmansyah, S.Pd",Nadia Salsabila Pramesti,553241123,XII RPL 1,IBI Darmajaya,6
22,"Rizky Nurmansyah, S.Pd",Falesia Zahra,553241057,XII TJAT,PT Slash Teknologi Indonesia (Balam Jaya),6
22,"Rizky Nurmansyah, S.Pd",Jessica Nur Adelia Putri,553241086,XII TJAT,PT Slash Teknologi Indonesia (Balam Jaya),6
22,"Rizky Nurmansyah, S.Pd",Hanny Cahaya Wiguna,553241074,XII ANIMASI,H.E. Art Studio (Animator),6
22,"Rizky Nurmansyah, S.Pd",Ratu Rahma Nur Ramadhani,553241144,XII ANIMASI,H.E. Art Studio (Animator),6
23,"Shinta Amelia Whardani, S.Pd",Adfen Widianto,553241001,XII TKJ 1,Diskominfo Provinsi Lampung,4
23,"Shinta Amelia Whardani, S.Pd",Denis Andila Saputra,553241046,XII TKJ 2,Telkom Akses Kedaton,4
23,"Shinta Amelia Whardani, S.Pd",Ahmad Faiz Pukuk Kusuma,553241008,XII RPL 1,Diskominfo Provinsi Lampung,4
23,"Shinta Amelia Whardani, S.Pd",Bahtera Wahyu Al Munir,553241037,XII RPL 2,Seputar Pringsewu,4
24,"Surya Aditia Pratama, S.Pd",Muhammad Rasya Pratama,553241118,XII TKJ 2,PGN COM dan Gasnet RO lampung,4
24,"Surya Aditia Pratama, S.Pd",Revan Pratama,553241148,XII TKJ 2,PGN COM dan Gasnet RO lampung,4
24,"Surya Aditia Pratama, S.Pd",Najwa Aulyya Ratu Pratama,553241126,XII RPL 1,Telkom Witel Lampung Kantor Enggal,4
24,"Surya Aditia Pratama, S.Pd",Reihan Firmansyah,553241147,XII RPL 1,Telkom Witel Enggal,4
25,"Tamara Azhar Azizah, S.Pd",Raihan Alfiansyah,553241142,XII RPL 1,Aurora Interior,2
25,"Tamara Azhar Azizah, S.Pd",Truly Honesty Falah Sahda,553241170,XII RPL 1,PLN ULP Pringsewu,2
26,"Wahyu Rahmat Hidayat, S.Kom",Emmanuel Febriyano,553241053,XII TKJ 1,Griyacom,8
26,"Wahyu Rahmat Hidayat, S.Kom",Iqbal Jafar Febriansyah Pratama,553241083,XII TKJ 1,Balai Monitoring Spektrum Frekuensi Radio Kelas II Lampung,8
26,"Wahyu Rahmat Hidayat, S.Kom",Genta Raihan Muzaki,553241071,XII TKJ 2,Telkom Akses Kedaton,8
26,"Wahyu Rahmat Hidayat, S.Kom",Muhamad Zidan Romadona,553241110,XII TKJ 2,Telkom Akses Kedaton,8
26,"Wahyu Rahmat Hidayat, S.Kom",Panji Pristianto,553241134,XII TKJ 2,Telkom Akses Kedaton,8
26,"Wahyu Rahmat Hidayat, S.Kom",Raihan Tami Wijaya,553241143,XII TKJ 2,Telkom Akses Kedaton,8
26,"Wahyu Rahmat Hidayat, S.Kom",Rava Saputra,553241145,XII TKJ 2,Telkom Akses Kedaton,8
26,"Wahyu Rahmat Hidayat, S.Kom",Syaifa Aulia Maharani,553241166,XII TKJ 2,Telkom Akses Kedaton,8
27,"Yuni Marlina, S.Pd",Ahmad Ghani Adam,553241009,XII TKJ 1,Telkom Akses Teluk Betung (Dipusatkan di Kedaton),8
27,"Yuni Marlina, S.Pd",Fabio Reri Aditya,553241054,XII TKJ 1,Telkom Akses Teluk Betung (Dipusatkan di Kedaton),8
27,"Yuni Marlina, S.Pd",Andhika Dwi Ramadhani,553241024,XII TKJ 2,Dinas Kominfo Pesawaran,8
27,"Yuni Marlina, S.Pd",Jesica Aprilia Rengganis,553241085,XII TKJ 2,BPS Pesawaran,8
27,"Yuni Marlina, S.Pd",Salsabila Maulida Asri,553241158,XII TJAT,BPN Pesawaran,8
27,"Yuni Marlina, S.Pd",Suheti Maya Sari,553241165,XII TJAT,BPN Pesawaran,8
27,"Yuni Marlina, S.Pd",Shifa Adelia Farenza,553241161,XII ANIMASI,Ventura Studios,8
27,"Yuni Marlina, S.Pd",Shinta Laura Dilla,553241162,XII ANIMASI,Ventura Studios,8
CSV;

        $lines = explode("\n", trim($rawCsv));
        $rows = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line) continue;
            $data = str_getcsv($line);
            if (count($data) < 6) continue;

            $rows[] = [
                'guru' => trim($data[1]),
                'siswa' => trim($data[2]),
                'nis' => trim($data[3]),
                'kelas' => trim($data[4]),
                'lokasi' => trim($data[5]),
            ];
        }

        $service = app(PrakerinRombelImportService::class);
        $result = $service->import($rows);

        $this->command->info("=== SEEDING BERHASIL ===");
        $this->command->info("Total Baris CSV: {$result['total_rows']}");
        $this->command->info("Total Siswa Terpetakan: {$result['matched_students']} siswa");
        $this->command->info("Total Industri Mitra: {$result['industri_count']} industri");
        $this->command->info("Total Rombel PKL Terbentuk: {$result['rombel_count']} rombel");
        $this->command->info("Total Penempatan Aktif: {$result['penempatan_count']} penempatan");

        if (!empty($result['missing_students'])) {
            $this->command->warn("Siswa tidak ditemukan: " . implode(', ', $result['missing_students']));
        }
    }
}
