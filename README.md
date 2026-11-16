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
