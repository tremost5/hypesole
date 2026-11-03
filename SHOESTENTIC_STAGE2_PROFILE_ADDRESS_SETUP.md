# SHOESTENTIC — Tahap 2: Profil & Alamat Customer

Patch ini melanjutkan project Laravel yang sudah memiliki login Customer melalui WhatsApp OTP.

## Isi patch

- `app/Http/Controllers/CustomerAccountController.php`
- `routes/web.php`
- `resources/views/customer/dashboard.blade.php`

Tidak ada migration baru dan tidak ada perubahan pada skema database.

## Cara memasang

1. Backup folder project terlebih dahulu.
2. Extract ZIP patch ini.
3. Salin folder `app`, `routes`, dan `resources` ke root project `C:\xampp\htdocs\shoestentic`, lalu pilih **Merge/Replace**.
4. Jangan menimpa file `.env`.
5. Dari terminal pada folder project, jalankan:

```bash
php artisan optimize:clear
php artisan route:list --path=customer
```

6. Login sebagai customer dan buka:

`http://localhost/shoestentic/public/customer/dashboard`

## Fitur

- Update nama customer.
- Nomor WhatsApp login ditampilkan sebagai nomor terverifikasi dan tidak bisa diedit dari form profil.
- Tambah, edit, dan hapus alamat.
- Set alamat utama.
- Saat alamat utama dihapus, alamat tersimpan terbaru otomatis menjadi alamat utama.
- Customer hanya dapat mengubah atau menghapus alamat miliknya sendiri.
- Nomor WhatsApp penerima dinormalisasi ke format `62...`.

## Database

Tidak perlu menjalankan `php artisan migrate` untuk patch ini. Patch memakai tabel `customers` dan `customer_addresses` yang sudah dibuat pada Tahap 1.

## Catatan

Jangan bagikan file `.env` saat mengirim ZIP project berikutnya. File tersebut dapat berisi APP_KEY, kredensial database, dan token provider.
