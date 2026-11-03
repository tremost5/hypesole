# SHOESTENTIC – Multi Shoe Care & UI/UX Update

## Isi patch
- Satu order Shoe Care dapat memuat 1–10 pasang sepatu.
- Setiap item menyimpan treatment, snapshot harga, brand/model/warna/ukuran, keluhan, staff, foto kondisi awal, foto proses/hasil, dan status masing-masing.
- Admin dapat memperbarui progres per sepatu dari halaman detail order.
- Customer dapat melihat progres per sepatu dan foto yang sudah diunggah.
- Order lama otomatis dimigrasikan menjadi satu item sepatu agar histori tetap terbaca.
- Penyegaran UI dashboard admin, navigasi admin, halaman penerimaan order, halaman detail dan riwayat Shoe Care, serta hero katalog publik.
- Tombol aksi utama diberi kontras tinggi, label jelas, dan ukuran tap minimum.

## File yang disertakan
Patch berisi file aplikasi yang berubah dan satu migration baru. Tidak menyertakan `.env`, database, `vendor`, `node_modules`, atau storage.

## Cara pasang
1. Backup folder project dan database terlebih dahulu.
2. Ekstrak ZIP patch.
3. Salin/merge isi folder `app`, `database`, `resources`, dan `routes` ke root project `C:\xampp\htdocs\shoestentic`.
4. Jangan menimpa `.env`.
5. Jalankan dari terminal project:

```bash
php artisan optimize:clear
php artisan migrate
php artisan view:clear
```

Migration baru: `2026_11_03_000008_create_shoe_care_order_items_table.php`.

Migration hanya membuat tabel `shoe_care_order_items` dan menyalin data setiap order Shoe Care lama ke satu item. Tidak menghapus atau mengubah tabel lama. Jangan jalankan `migrate:fresh`, `db:wipe`, atau reset database.

## Tes manual
1. Admin > Order Shoe Care > Buat Order Baru.
2. Isi data customer.
3. Tambahkan dua atau tiga kartu sepatu, pilih treatment berbeda bila perlu.
4. Simpan order.
5. Buka detail order dan ubah status satu item; pastikan item lain tidak berubah.
6. Login customer dengan nomor WhatsApp yang terhubung dan periksa Order Shoe Care Saya.
7. Periksa foto kondisi awal dan foto proses/hasil.

## Verifikasi yang dilakukan
- 94 file PHP lolos `php -l`.
- Seluruh 27 file Blade berhasil dikompilasi dengan BladeCompiler.
- Belum dilakukan pengujian browser end-to-end di komputer pengguna. Tes visual dan alur transaksi tetap perlu dilakukan di XAMPP.
