# SIAKAD Terdistribusi

Sistem Layanan Informasi Akademik (SIAKAD) berbasis arsitektur **basis data terdistribusi**, dikembangkan untuk mengatasi *bottleneck* pemrosesan transaksional saat periode pengisian Kartu Rencana Studi (KRS). Proyek ini disusun sebagai Tugas Akhir Ujian Akhir Semester mata kuliah Perancangan Basis Data Terdistribusi.

> 📄 Dokumen SRS lengkap dan Laporan Analisis Kesesuaian (Gap Analysis) tersedia terpisah sebagai lampiran.

---

## Arsitektur Sistem

Sistem terdiri atas **3 Node Regional** yang otonom dan **1 Node Pusat (GCS)**, dihubungkan melalui Nginx Gateway dengan routing berbasis subdomain:

```
Web Browser
     │
     ▼
Nginx Gateway (routing subdomain)
     │
     ├── Regional 1 — Teknik & Ilmu Komputer   → Laravel + Redis + PostgreSQL
     ├── Regional 2 — Ekonomi & Bisnis          → Laravel + Redis + PostgreSQL
     └── Regional 3 — Kedokteran & Kesehatan    → Laravel + Redis + PostgreSQL
                    │
                    ▼ (replikasi master data & sinkronisasi transaksi terjadwal)
          GCS Pusat — PostgreSQL (Konsolidasi & Master Data)
```

Setiap Node Regional memiliki basis data PostgreSQL **fisik terpisah** dan beroperasi mandiri (kegagalan satu node tidak memengaruhi node lain). Node Pusat berfungsi sebagai repositori agregasi *read-only* untuk data KRS/Nilai serta sumber otoritatif master data (Mata Kuliah, Dosen, Ruangan).

---

## Teknologi

| Komponen | Teknologi |
|---|---|
| Framework Aplikasi | Laravel 12 (PHP 8.2) |
| Basis Data | PostgreSQL 15 (4 instance independen) |
| Message Broker / Cache | Redis 7 |
| Reverse Proxy / Gateway | Nginx |
| Containerization | Docker & Docker Compose |
| Frontend | Blade + Tailwind CSS (CDN) + Vanilla JS |

---

## Fitur

### 👨‍🎓 Mahasiswa
- Dashboard KRS dengan status kuota kelas *real-time*
- Pengisian KRS melalui antrean FIFO (Redis Queue) — anti *race condition*
- Validasi otomatis: status keuangan, kuota kelas, prasyarat mata kuliah
- Kartu Hasil Studi (KHS) & perhitungan IPK otomatis

### 👨‍🏫 Dosen
- Kelola kelas yang diampu per semester
- Input & rekap presensi per pertemuan
- Input nilai dan finalisasi (nilai final bersifat *immutable*)

### 🏢 Admin BAAK (Regional)
- Kelola Mata Kuliah, Dosen, Ruangan
- Buka Kelas baru (assign dosen, ruangan, kuota, semester)
- Kalender akademik (buka/tutup periode KRS)
- Revisi nilai pasca-finalisasi (tercatat di audit log)
- Kelola status keuangan mahasiswa
- Ekspor data ke format PDDIKTI Neo Feeder

### 🌐 GCS Pusat (BAAK Pusat)
- Monitoring status online/offline tiap Node Regional
- Agregasi data KRS & Nilai lintas regional
- Kelola master data terpusat dengan replikasi granular per regional
- Kelola akun pengguna (mahasiswa/dosen/BAAK) tiap regional
- Sinkronisasi terjadwal ke tabel konsolidasi (dengan mekanisme *retry* & eskalasi kegagalan)
- Audit log terpusat, *append-only*

---

## Instalasi & Menjalankan Proyek

### Prasyarat
- Docker Desktop
- PHP 8.2+ & Composer (untuk instalasi awal dependency)

### Langkah Setup

```bash
# 1. Clone repository
git clone https://github.com/USERNAME/siakad-terdistribusi.git
cd siakad-terdistribusi

# 2. Salin file environment
cp .env.example .env

# 3. Install dependency PHP
composer install

# 4. Build & jalankan container
docker-compose up -d

# 5. Generate application key
docker-compose exec laravel.test php artisan key:generate

# 6. Jalankan migration & seeder ke semua node
docker-compose exec laravel.test php artisan migrate --seed
docker-compose exec laravel.test php artisan migrate --seed --database=pgsql_r2
docker-compose exec laravel.test php artisan migrate --seed --database=pgsql_r3
docker-compose exec laravel.test php artisan migrate --database=pgsql_pusat

# 7. Jalankan queue worker (di terminal terpisah)
docker-compose exec laravel.test php artisan queue:work redis --verbose
```

### Konfigurasi Hosts (routing subdomain)

Tambahkan baris berikut ke `C:\Windows\System32\drivers\etc\hosts` (jalankan sebagai Administrator):
```
127.0.0.1 teknik.siakad.test
127.0.0.1 ekonomi.siakad.test
127.0.0.1 kesehatan.siakad.test
127.0.0.1 pusat.siakad.test
```

### Akun Uji Coba

| Role | Email | Password |
|---|---|---|
| Mahasiswa (R1) | luthfi@student.sttc.ac.id | password123 |
| Dosen (R1) | siti@dosen.sttc.ac.id | password123 |
| BAAK Regional 1 | admin@baak.sttc.ac.id | password123 |
| BAAK Pusat | pusat@baak.sttc.ac.id | password123 |

---

## Struktur Basis Data

Setiap Node Regional menerapkan **fragmentasi horizontal** berbasis `id_regional` pada tabel transaksional (`mahasiswa`, `kelas`, `krs`, `nilai`), sementara tabel master (`mata_kuliah`, `dosen`, `ruangan`) direplikasi secara selektif dari Node Pusat. Tabel `audit_log` bersifat *append-only* (diproteksi PostgreSQL `RULE`) dan hanya berada di Node Pusat.

---

## Catatan Pengembangan

Beberapa aspek non-fungsional lanjutan (autentikasi JWT terpusat, PostgreSQL Logical Replication native, *High Availability*/node standby, enkripsi TLS antar-node) belum diimplementasikan secara penuh karena keterbatasan waktu pengembangan. Rincian lengkap kesesuaian requirement SRS terhadap implementasi didokumentasikan pada **Laporan Analisis Kesesuaian** terpisah.

---

## Kontributor

**Luthfi Isa Majid**
Program Studi Informatika — Sekolah Tinggi Teknologi Cipasung
