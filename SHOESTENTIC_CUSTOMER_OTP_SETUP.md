# SHOESTENTIC — Customer WhatsApp OTP (Tahap 1)

## Yang ditambahkan
- Guard autentikasi `customer` yang terpisah dari guard `web`.
- Tabel `customers`, `customer_otps`, dan `customer_addresses`.
- Model `Customer`, `CustomerOtp`, dan `CustomerAddress`.
- `FonnteService` untuk mengirim OTP.
- Halaman login, verifikasi OTP, dan dashboard Customer.
- Rate limit permintaan OTP dan verifikasi.
- OTP 6 digit, masa berlaku 5 menit, hash di database, sekali pakai, maksimal 5 percobaan.
- Normalisasi nomor Indonesia ke format `62...`.
- Customer dibuat otomatis setelah nomor berhasil diverifikasi.

## Konfigurasi lokal
Tambahkan ke file `.env` lokal (jangan commit file `.env`):

```dotenv
FONNTE_TOKEN=ISI_TOKEN_FONNTE
FONNTE_ENDPOINT=https://api.fonnte.com/send
```

Setelah mengubah `.env`, jalankan:

```bash
php artisan config:clear
```

## Route Customer
- `GET /customer/login`
- `POST /customer/otp`
- `GET /customer/otp`
- `POST /customer/otp/verify`
- `GET /customer/dashboard` (guard `customer`)
- `POST /customer/logout` (guard `customer`)

## Database
Tiga migration baru tersedia, tetapi **belum dijalankan** oleh proses pembuatan patch ini.

Sebelum menjalankan migration:
1. Pastikan `.env` menunjuk ke database lokal `shoestentic`.
2. Backup database.
3. Periksa SQL/rencana migration dengan:

```bash
php artisan migrate --pretend
```

4. Jika target dan tabel yang akan dibuat sudah benar, jalankan:

```bash
php artisan migrate
```

Migration hanya membuat tabel baru `customers`, `customer_otps`, dan `customer_addresses`. Tidak ada perintah `migrate:fresh`, `db:wipe`, `DROP`, atau truncate dalam perubahan ini.

## Pengujian
Test unit Fonnte ditambahkan pada `tests/Unit/FonnteServiceTest.php`.
Jalankan:

```bash
php artisan test --filter=FonnteServiceTest
```

Test integrasi yang menggunakan database perlu dijalankan pada environment lokal yang memiliki driver PDO MySQL atau SQLite.

## Catatan
- Jangan mengisi token asli ke `.env.example`.
- Jangan commit `.env`.
- Pastikan token Fonnte aktif dan perangkat/akun WhatsApp Fonnte siap sebelum menguji pengiriman sungguhan.
- Login internal (`users`) tidak diubah pada tahap ini.
- Halaman dashboard Customer saat ini hanya placeholder untuk tahap Shoe Care dan Store berikutnya.
