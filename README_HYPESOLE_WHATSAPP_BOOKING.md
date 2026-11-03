# HYPESOLE — Guest Booking via WhatsApp

Patch untuk landing page HYPESOLE:
- CTA hero dan CTA bawah diseragamkan menjadi BOOKING ONLINE SEKARANG, BOOKING VIA WHATSAPP, dan LIHAT PRODUK.
- Booking online tetap menuju login pelanggan.
- Booking via WhatsApp membuka formulir publik tanpa login.
- Form menerima data pelanggan, nomor WhatsApp, alamat, pickup/drop-off, tanggal pilihan, catatan, dan 1–10 detail sepatu (layanan, jenis, brand, model, ukuran, warna, keluhan).
- Setelah submit, browser diarahkan ke WhatsApp HYPESOLE dengan pesan booking terisi. Pelanggan tetap perlu menekan tombol Kirim di WhatsApp.
- Form ini tidak membuat transaksi/order database dan tidak mengirim pesan otomatis dari server. Data booking disampaikan langsung oleh pelanggan ke WhatsApp admin.

## Cara pasang
1. Backup project.
2. Extract ZIP ke root project Laravel dan merge file.
3. Tambahkan nomor WhatsApp admin di `.env` (jangan mengganti isi `.env` yang lain):

   `HYPESOLE_WHATSAPP_NUMBER=6281234567890`

   Gunakan nomor WhatsApp bisnis dalam format internasional tanpa `+`, spasi, atau strip. Ganti contoh di atas dengan nomor HYPESOLE yang benar.
4. Jalankan:

   ```bash
   php artisan optimize:clear
   php artisan route:list --name=guest.booking.whatsapp
   ```
5. Buka landing page, uji tombol BOOKING VIA WHATSAPP, isi form, lalu pastikan WhatsApp terbuka dengan nomor admin dan ringkasan data yang benar.

## Catatan keamanan & data
- Tidak ada migration atau perubahan database.
- Tidak ada penghapusan/pengubahan data pelanggan atau order.
- Form memakai CSRF dan rate limit 8 submit/menit per IP.
- Nomor WhatsApp admin bukan token/rahasia; simpan di `.env` agar mudah diganti.
- Pastikan layanan HYPESOLE aktif pada master data agar muncul di dropdown.
- Tanggal pilihan bersifat permintaan pelanggan, bukan jadwal yang otomatis terkonfirmasi.
