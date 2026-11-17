# DompetKu

Aplikasi dompet digital untuk kantin kampus.

## Tech Stack
- Backend: Laravel (PHP)
- Database: MySQL
- Containerization: Docker & Docker Compose
- CI/CD: Jenkins
- Version Control: Git & GitHub
- Container Registry: Docker Hub
- Webhook Tunneling: Ngrok

## Fitur
- Registrasi Akun — Pengguna dapat membuat akun dompet digital.
- Top-Up Saldo — Pengguna dapat menambahkan saldo ke dompet.
- Transfer Saldo — Pengguna dapat mentransfer saldo ke akun lain.
- Cek Saldo — Pengguna dapat melihat saldo yang tersedia.
- Riwayat Transaksi — Pengguna dapat melihat riwayat transaksi beserta saldo sebelum dan sesudah transaksi.

## NOTED
- Saldo pengguna tidak boleh negatif.
- Transfer harus bersifat atomik: debit dan kredit berhasil bersama atau gagal bersama.
- Nominal transaksi minimal Rp1.000.
- Batas transfer maksimal Rp2.000.000 per hari per akun.
- Transaksi dengan idempotency key yang sama tidak boleh diproses dua kali.

## Menjalankan Aplikasi Menggunakan Docker
- Buat file `.env` berdasarkan `.env.example`:

```bash
cp .env.example .env
```

- Build Docker image:

```bash
docker compose build app
```

- Generate application key:

```bash
docker compose run --rm --no-deps app php artisan key:generate --show
```

- Salin hasil key dari terminal dan masukkan ke dalam file `.env`:

```env
APP_KEY=base64:hasil_key_yang_diperoleh
```

- Jalankan container:

```bash
docker compose up -d
```

- Jalankan database migration:

```bash
docker compose exec app php artisan migrate --force
```

- Masukkan data contoh:

```bash
docker compose exec app php artisan db:seed --class=WalletSeeder --force
```

- Periksa status container:

```bash
docker compose ps
```

Pastikan:
-- Container `dompetku-app` berstatus `Up`.
-- Container `dompetku-db` berstatus `Up (healthy)`.

### Mengakses Aplikasi

Buka browser dan akses:

- **Health Check:** http://localhost:8000/health
- **DompetKu:** http://localhost:8000

### Menghentikan Aplikasi

- Untuk menghentikan seluruh container:

```bash
docker compose down
```

### Menjalankan Ulang Aplikasi

- Jika container sudah dihentikan:

```bash
docker compose up -d
```

- Jika terdapat perubahan source code:

```bash
docker compose up -d --build
```

- Jika terdapat migration baru:

```bash
docker compose exec app php artisan migrate --force
```

### Catatan

- Setiap anggota memiliki database lokal masing-masing.
- File `.env` tidak boleh diunggah ke GitHub.
- Gunakan `.env.example` sebagai template konfigurasi.
- Pastikan Docker Desktop aktif sebelum menjalankan aplikasi.
- Database menggunakan persistent volume sehingga data tetap tersimpan meskipun container dihentikan.
- Jalankan `WalletSeeder` hanya jika membutuhkan data contoh pada database baru.
- Jangan gunakan `docker compose down -v` jika ingin mempertahankan data database.
