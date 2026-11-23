# Rencana Pengujian DompetKu

## Ruang lingkup

Pengujian mencakup health check, registrasi, cek saldo, top-up, transfer, dan riwayat transaksi. Kriteria fitur merujuk ke [Fitur dan Aturan Bisnis DompetKu](fitur-aturan-bisnis.md).

## Skenario dan status

| ID | Skenario | Data uji dan langkah | Hasil yang diharapkan | Status aktual |
| --- | --- | --- | --- | --- |
| QA-01 | Health check | Panggil `GET /health` saat koneksi database tersedia. | HTTP 200, respons JSON mengidentifikasi DompetKu dan menyatakan database terhubung. | Lulus melalui `Tests\Feature\HealthTest` di Docker dengan SQLite in-memory. |
| QA-02 | Registrasi | Daftarkan pengguna baru dengan data valid. | Akun terbentuk dengan satu dompet bersaldo Rp0. | Terblokir. Route registrasi belum tersedia pada revisi yang diperiksa. |
| QA-03 | Top-up di bawah minimum (AB-03) | Catat saldo, lalu ajukan top-up Rp999. Ulangi dengan Rp1.000 sebagai kontrol batas. | Rp999 ditolak dan saldo tetap. Rp1.000 diterima dan saldo bertambah Rp1.000 jika syarat lainnya terpenuhi. | Terblokir. Route top-up belum tersedia. |
| QA-04 | Transfer melebihi saldo (AB-01) | Dengan saldo pengirim Rp5.000, transfer Rp6.000 ke pengguna lain. | Transfer ditolak dan saldo kedua akun tetap. | Terblokir. Route transfer belum tersedia. |
| QA-05 | Limit transfer harian (AB-04) | Siapkan total transfer keluar Rp1.999.000 dalam satu hari dan saldo yang cukup. Ajukan dua transfer Rp1.000 secara berurutan pada hari yang sama. | Transfer pertama diterima. Transfer kedua ditolak dan kedua saldo tetap seperti setelah transfer pertama. | Terblokir. Route transfer belum tersedia. |
| QA-06 | Idempotensi transfer (AB-05) | Kirim transfer valid, lalu ulangi permintaan dengan pengirim, penerima, nominal, dan idempotency key yang sama setelah transfer pertama berhasil. | Kedua saldo berubah sekali dan hanya satu transfer tercatat. | Terblokir. Route transfer belum tersedia. |
| QA-07 | Pencatatan riwayat transaksi | Lakukan top-up dan transfer yang berhasil, lalu buka riwayat dompet yang terkait. | Transaksi yang berhasil tercatat dengan nominal dan saldo sebelum serta sesudah yang sesuai. | Terblokir. Route transaksi dan riwayat belum tersedia. |
| QA-08 | Cek saldo | Cek saldo setelah registrasi dan cek lagi setelah top-up yang berhasil. | Saldo menampilkan Rp0 setelah registrasi dan bertambah sesuai nominal top-up. | Terblokir. Route wallet belum tersedia. |
| QA-09 | Transfer di bawah minimum (AB-03) | Dengan saldo cukup, ajukan transfer Rp999. Ulangi dengan Rp1.000 sebagai kontrol batas. | Rp999 ditolak dan kedua saldo tetap. Rp1.000 diterima jika syarat lainnya terpenuhi. | Terblokir. Route transfer belum tersedia. |
| QA-10 | Atomisitas transfer (AB-02) | Dengan saldo cukup, jalankan transfer valid. Pada percobaan terpisah, simulasikan kegagalan saat kredit penerima. | Transfer valid menyimpan debit dan kredit. Pada percobaan yang gagal, kedua saldo kembali ke nilai sebelum percobaan. | Terblokir. Route transfer belum tersedia. |

Status **Terblokir** berarti skenario belum bisa dijalankan. Pada 23 November 2026, `php artisan route:list --except-vendor` untuk commit `92b70e0` hanya menampilkan `GET|HEAD /` dan `GET|HEAD /health`.

Pengecekan ulang untuk Test 2–4 pada 23 November 2026 mencakup branch QA lokal (berbasis `6fe7dea`) dan branch `Backend` di GitHub (`1b259c7`). Kedua revisi belum memiliki route registrasi, top-up, atau transfer. Status QA-02, QA-03, dan QA-04 tetap **Terblokir**. Untuk mulai mengotomatisasi ketiganya, gunakan kontrak route, payload, autentikasi, dan respons dari implementasi Backend yang tersedia.

Pemeriksaan manual QA-01 mencatat `status: success` sebagai respons aktual. `HealthTest` memeriksa HTTP 200, `application: DompetKu`, dan `database: connected`. Nilai field `status` masih menunggu konfirmasi kontrak respons. Koneksi MySQL belum diuji pada pemeriksaan ini.

## Pengujian otomatis

PHPUnit menggunakan SQLite in-memory melalui `phpunit.xml`. Dockerfile menjalankan `php artisan test` pada target `test` dengan PHP 8.3. Jenkinsfile memakai target Docker yang sama pada tahap Test.

Pada 23 November 2026, suite awal menghasilkan **2 passed (2 assertions)**. Setelah `HealthTest` ditambahkan, suite lokal menghasilkan **3 passed (5 assertions)** di image `dompetku-test:latest` (PHP 8.3.37), dengan direktori test, route, dan konfigurasi PHPUnit dari workspace. Hasil Jenkins belum diverifikasi.

```bash
docker build --target test --progress=plain -t dompetku:qa-local .
docker run --rm dompetku:qa-local php artisan test
```

Target studi kasus adalah minimal lima test otomatis, termasuk minimal dua test aturan bisnis, dengan eksekusi di tahap Test Jenkins. Untuk setiap hasil, catat ID skenario, tanggal, commit, lingkungan, hasil aktual, dan referensi bukti.
