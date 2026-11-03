# HYPESOLE WhatsApp Booking – Photo Fix

Patch lanjutan untuk memperbaiki kontras tombol dan menambahkan upload foto per pasang sepatu.

## Perubahan
- Tombol BOOKING VIA WHATSAPP memakai warna hijau dan teks putih dengan inline `!important` agar tidak tertimpa CSS global.
- Form memakai `multipart/form-data`.
- Setiap pasang sepatu dapat menyertakan maksimal 3 foto (JPG/JPEG/PNG/WEBP), masing-masing maksimal 3 MB.
- Foto disimpan pada disk `local` (storage/app/private pada konfigurasi project saat ini), bukan folder public.
- Pesan WhatsApp menyertakan tautan foto bertanda tangan yang berlaku 7 hari. Pelanggan tetap harus menekan tombol Kirim di WhatsApp.
- Validasi server-side tetap berlaku; tidak ada migration atau perubahan database.

## Pemasangan
Extract dan merge folder patch ke root project Laravel, menimpa file yang sama. Jalankan:

```bash
php artisan optimize:clear
php artisan route:list --name=guest.booking.whatsapp
```

Pastikan disk `local` mengarah ke storage private sesuai konfigurasi project. Jangan mengubah `.env` atau database.

## Catatan
Tautan foto membutuhkan website yang dapat diakses publik melalui HTTPS agar admin dapat membukanya dari WhatsApp. Di localhost, tautan hanya bisa dibuka dari komputer lokal. File demo tersimpan privat; tautan foto kedaluwarsa setelah 7 hari. File lama perlu kebijakan pembersihan terjadwal jika fitur ini dipakai jangka panjang.
