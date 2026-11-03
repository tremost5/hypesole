# SHOESTENTIC - hasil pemeriksaan dan perbaikan

Patch ini dibuat dari source `shoestentic(9).zip`.

## File yang diperbaiki

1. `routes/web.php`
   - Menambahkan route POST `customer.orders.items.approval` ke method `CustomerOrderController::approveItem`.
   - Sebelumnya form persetujuan harga pada halaman detail order pelanggan memanggil route yang tidak terdaftar.
2. `resources/js/app.js`
   - Registrasi service worker sekarang menggunakan URL manifest sebagai basis.
   - Mencegah browser mencari `service-worker.js` di folder `/produk/...` saat halaman produk dibuka.
   - Registrasi hanya dilakukan pada halaman yang memuat link manifest (storefront).

## Cara memasang

1. Backup folder project terlebih dahulu.
2. Ekstrak patch dan salin folder `routes` serta `resources` ke project, pilih merge/replace file yang sama.
3. Jangan mengubah `.env`, database, atau menjalankan migration untuk patch ini. Tidak ada perubahan database.
4. Jalankan dari folder project:

```bash
npm install
npm run build
php artisan optimize:clear
```

5. Buka ulang halaman storefront dan detail produk. Periksa DevTools > Application > Service Workers dan pastikan service worker terdaftar pada scope aplikasi.
6. Uji pelanggan: buka detail order yang memiliki penawaran harga, lalu coba tombol Setujui/Tolak.

## Hasil audit statis

- Seluruh file PHP dan Blade yang diekstrak lolos `php -l` (pemeriksaan sintaks PHP). Ini belum membuktikan seluruh alur Laravel berjalan.
- Build Vite belum dapat diverifikasi di lingkungan audit karena arsip `node_modules` tidak memuat paket native Rollup untuk Linux; jalankan `npm install` lalu `npm run build` di komputer Windows/XAMPP.
- Test suite otomatis belum dapat dijalankan di lingkungan audit karena PHP CLI yang tersedia tidak memiliki ekstensi PDO SQLite.
- Temuan keamanan penting: foto kondisi sepatu dan bukti pengiriman disimpan pada disk `public` dan ditampilkan melalui URL `/storage/...`. Artinya file tersebut dapat diakses langsung oleh siapa pun yang mengetahui URL, tanpa melewati autentikasi halaman order. Sebelum aplikasi dipakai dengan foto pelanggan sungguhan, sebaiknya pindahkan file bukti ke private storage dan layani melalui controller yang memeriksa kepemilikan order/izin admin.
- Arsip source juga memuat beberapa file di `public/storage/` dan `database/database.sqlite`. Pastikan file contoh/hasil uji tersebut tidak berisi data atau foto pelanggan sungguhan sebelum ZIP dibagikan atau diunggah ke hosting.

Tidak ada migration, penghapusan data, atau perubahan `.env` pada patch ini.
