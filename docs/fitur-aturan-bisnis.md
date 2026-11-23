# Fitur dan Aturan Bisnis DompetKu

DompetKu adalah dompet digital untuk kantin kampus dalam studi kasus K03. Dokumen ini menjadi acuan QA untuk menilai perilaku yang diharapkan. Kelima aturan bisnis di bawah ini belum diverifikasi melalui pengujian fitur.

## Fitur

| Fitur | Perilaku yang diharapkan |
| --- | --- |
| Registrasi | Pengguna yang berhasil mendaftar memperoleh satu dompet dengan saldo awal Rp0. |
| Cek saldo | Pengguna dapat melihat saldo terkini pada dompetnya. |
| Top-up | Top-up yang berhasil menambah saldo sebesar nominal yang diminta. |
| Transfer | Pengguna dapat memindahkan saldo ke akun lain. Jika transfer berhasil, saldo pengirim berkurang dan saldo penerima bertambah sebesar nominal transfer. |
| Riwayat transaksi | Pengguna dapat melihat transaksi pada dompetnya beserta saldo sebelum dan sesudah transaksi. |

## Aturan bisnis

| ID | Aturan | Kriteria penerimaan |
| --- | --- | --- |
| AB-01 | Saldo tidak boleh negatif. | Jika nominal transfer melebihi saldo pengirim, transfer ditolak dan kedua saldo tetap. |
| AB-02 | Debit pengirim dan kredit penerima harus berhasil bersama. | Saat transfer berhasil, kedua perubahan saldo tersimpan. Jika salah satu perubahan gagal, saldo kedua akun tetap sama seperti sebelum percobaan transfer. |
| AB-03 | Nominal minimum top-up dan transfer adalah Rp1.000. | Nominal Rp999 ditolak tanpa perubahan saldo. Nominal Rp1.000 memenuhi syarat minimum dan dapat diproses jika syarat lainnya terpenuhi. |
| AB-04 | Total transfer keluar maksimal Rp2.000.000 per akun per hari. | Jika total transfer keluar hari itu Rp1.999.000, transfer Rp1.000 berikutnya memenuhi batas harian selama syarat lain terpenuhi. Setelah total mencapai Rp2.000.000, transfer Rp1.000 berikutnya ditolak dan kedua saldo tetap. |
| AB-05 | Transfer dengan idempotency key yang sama hanya diproses sekali. | Jika permintaan transfer yang sama dikirim ulang setelah berhasil dengan idempotency key yang sama, saldo pengirim dan penerima masing-masing berubah hanya sekali. |

## Detail untuk dikonfirmasi dengan Backend

- Zona waktu dan waktu pergantian hari untuk menghitung batas transfer harian.
- Cakupan idempotency key, masa berlakunya, dan respons jika key yang sama dikirim dengan isi permintaan berbeda.
