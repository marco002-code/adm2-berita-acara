# RANCANGAN TEKNIS: APLIKASI E-BERITA ACARA & DAFTAR HADIR UJIAN
**Studi Kasus:** Pelaksanaan Asesmen Nasional (ANBK) / TKA Kemendikdasmen  
**Target Stack:** Laravel, Nginx, HestiaCP (Debian Server)

---

## 1. ARSITEKTUR & ALUR EKOSISTEM

Sistem dirancang agar berjalan di atas infrastruktur server Debian menggunakan panel HestiaCP dengan pemisahan akses melalui Nginx Reverse Proxy dan PHP-FPM.

```text
 [ Klien / Browser Guru & Proktor ]
        │ (HTTPS)
        ▼
 [ Nginx (HestiaCP Reverse Proxy) ]
        │
        ├─► Static Assets (/css, /js, /build) ──► Direct Serve
        │
        └─► PHP-FPM (Laravel Application /public/index.php)
                   │
                   ▼
         [ Database MySQL / MariaDB / SQLite ] 
         (Tabel: rooms, exam_sessions, participants, attendances, minutes_of_events, users)
```

---

## 2. LATAR BELAKANG & ANALISIS MASALAH (PROBLEM STATEMENT)

Pelaksanaan ujian skala institusi (seperti Asesmen Nasional Berbasis Komputer/ANBK, TPM, atau TKA Kemendikdasmen) selalu melibatkan proses administratif yang krusial: pencatatan kehadiran peserta serta penyusunan Berita Acara Pelaksanaan Ujian per sesi dan per ruangan.

Di lapangan, prosedur konvensional menghadapi serangkaian kendala mendasar:

### A. Inefisiensi Input Data (Presensi Konvensional)
* **Masalah:** Pada umumnya tingkat kehadiran siswa mencapai 95%–99%. Sistem presensi konvensional mewajibkan proktor atau pengawas mencentang *setiap satu per satu* siswa yang hadir (misalnya 40 anak per ruangan). Ini membuang waktu 10–15 menit di awal setiap sesi ujian.
* **Akibat:** Beban administratif proktor meningkat di waktu-waktu krusial saat harus mengawasi konektivitas server client dan kesiapan teknis peserta ujian.

### B. Inkonsistensi Angka (Human Error Perhitungan Manual)
* **Masalah:** Pengawas sering keliru menghitung manual antara:
  $$\text{Jumlah Seharusnya Hadir} = \text{Jumlah Hadir} + \text{Jumlah Tidak Hadir}$$
* **Akibat:** Muncul lembar Berita Acara cacat administratif karena selisih angka kehadiran dan daftar siswa tidak sinkron dengan data presensi fisik.

### C. Catatan Problem / Insiden yang Tidak Terstandarisasi
* **Masalah:** Kendala teknis (gangguan kelistrikan, PC client restart, kartu login bermasalah, atau peserta terlambat) sering kali tidak dicatat secara akurat atau terlewatkan dalam Berita Acara karena keterbatasan format kertas.
* **Akibat:** Tidak ada rekam jejak formal jika terjadi sengketa hasil ujian atau verifikasi dari dinas pendidikan.

### D. Redundansi Pengisian Data Petugas
* **Masalah:** Nama proktor, pengawas, nomor induk (NBM/NIP), dan nama kepala sekolah harus ditulis tangan berulang kali untuk setiap sesi ujian (bisa mencapai belasan sesi selama masa ujian berlangsung).

---

## 3. SOLUSI & PARADIGMA "INVERSE ATTENDANCE"

Untuk menyelesaikan seluruh kendala di atas, sistem mengimplementasikan pendekatan efisiensi tinggi:

### 1. Paradigma Inverse Attendance (Pencatatan Presensi Terbalik)
* **Aturan Utama:** Database **hanya menyimpan record peserta yang TIDAK HADIR**.
* Peserta yang tidak dicentang otomatis dianggap **HADIR**.
* **Dampak Positif:** Jika seluruh 40 peserta dalam satu ruangan hadir lengkap, proktor **cukup menekan tombol Simpan tanpa perlu mencentang satu pun**, selesai dalam 2 detik. Jika ada 1 anak sakit, proktor hanya mencentang 1 nama tersebut dan mengisi keterangannya.

### 2. Kalkulasi Otomatis Berita Acara
* Sistem menghitung statistik kehadiran secara otomatis dan *real-time*:
  $$\text{Expected} = \text{Count}(\text{participants where room\_id \& session\_id})$$
  $$\text{Absent} = \text{Count}(\text{attendances where room\_id \& session\_id})$$
  $$\text{Present} = \max(0, \text{Expected} - \text{Absent})$$
* Berita Acara dan Daftar Hadir otomatis 100% konsisten tanpa risiko salah hitung.

### 3. Modul Catatan Problem & Insiden Khusus
* Berita acara dilengkapi kolom catatan insiden khusus yang langsung masuk ke dokumen cetak resmi (Kop Surat Kementerian, rincian sesi, daftar siswa absen berpenjelasan, serta kolom pengesahan proktor, pengawas, dan kepala sekolah).

### 4. Database Petugas Terintegrasi
* Proktor dan Pengawas dapat dipilih langsung dari daftar akun/guru terdaftar (`proctors` & `users`), sehingga nama dan NIP/NBM terisi otomatis secara instan.

---

## 4. SKEMA DATA & STRUKTUR TABEL

1. **`rooms`**: Master ruangan (kode ruang, nama lab, kapasitas).
2. **`exam_sessions`**: Jadwal sesi (nama sesi, jam mulai, jam selesai, tanggal, mata uji).
3. **`participants`**: Data peserta ujian per sesi & ruangan (nomor tes, nama, rombel/kelas, NISN).
4. **`attendances`**: Hanya menyimpan peserta yang tidak hadir:
   * `participant_id`, `exam_session_id`, `room_id`, `status` (`tidak_hadir`), `reason` (`Sakit`/`Izin`/`Tanpa Keterangan`), `notes`.
5. **`minute_of_events`**: Berita acara per sesi & ruang:
   * `proctor_name`, `proctor_nip`, `supervisor_name`, `supervisor_nip`, `headmaster_name`, `headmaster_nip`, `incident_notes`, `status`.
6. **`app_settings`**: Konfigurasi global nama asesmen, nama sekolah, tahun ajaran, dan identitas kepala sekolah.

---

## 5. PANDUAN PENGGUNAAN & CETAK DOKUMEN

1. **Presensi Peserta:**
   * Masuk ke menu **Presensi Peserta**.
   * Pilih Sesi Ujian dan Ruangan.
   * Siswa ditampilkan dengan status default *Hadir*.
   * Centang kotak pada siswa yang berhalangan hadir, pilih alasan (Sakit, Izin, Tanpa Keterangan) dan catatan bila ada.
   * Klik **Simpan Presensi**.
2. **Pengisian & Cetak Berita Acara:**
   * Masuk ke menu **Berita Acara**.
   * Pilih Sesi dan Ruangan yang bersangkutan.
   * Pilih Proktor dari dropdown cepat, isi nama Pengawas Ruang.
   * Tambahkan **Catatan Kejadian Khusus / Problem** selama pelaksanaan ujian jika ada kendala teknis atau catatan khusus.
   * Klik **Simpan Berita Acara**.
   * Tombol **Cetak Dokumen Resmi** akan aktif untuk mencetak dokumen format A4 standar resmi kementerian siap tanda tangan.