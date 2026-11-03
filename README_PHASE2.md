# SHOESTENTIC — Go-Live Phase 2 Patch

Patch untuk source shoestentic(20261103-085657).zip setelah Phase 1.

## Perubahan
- Booking Shoe Care oleh customer di /customer/booking, maksimal 10 sepatu per order.
- Booking memakai tabel services, shoe_care_orders, shoe_care_order_items yang sudah ada. Tidak ada migration.
- Harga awal tersimpan sebagai snapshot; persetujuan harga final tetap dilakukan per item setelah admin mengirim quote.
- Foto baru Shoe Care dan shipment disimpan pada disk private.
- Endpoint media customer/admin memeriksa kepemilikan order atau autentikasi admin.
- Command penyalinan foto lama tanpa menghapus file asli.

## Pemasangan
1. Backup project dan database.
2. Extract ZIP ke root C:\xampp\htdocs\shoestentic, gabungkan folder. Jangan timpa .env.
3. Jalankan:
   php artisan optimize:clear
   php artisan route:list
   php artisan shoestentic:media-to-private --dry-run
4. Tinjau daftar file yang hilang. Jika hasil sesuai, jalankan:
   php artisan shoestentic:media-to-private
   Command hanya COPY file; tidak menghapus file public dan tidak mengubah database.
5. Jalankan php artisan test dan npm run build.
6. Uji booking satu dan beberapa sepatu, order masuk dashboard admin, quote per item, dan approval pelanggan.
7. Uji foto sebagai pemilik order, customer lain, admin, dan kurir.

## Penting: foto lama dan public exposure
Endpoint baru melayani foto melalui pemeriksaan akses. File lama tetap di public setelah disalin demi kompatibilitas. Selama public/storage symlink masih aktif, URL lama /storage/... masih dapat diakses langsung.

Jangan hapus symlink atau file lama sebelum seluruh file berhasil disalin dan semua halaman diuji. Untuk produksi, tutup akses web langsung ke /storage/shoe-care-orders dan /storage/shoe-care-shipments melalui konfigurasi web server, tanpa memutus akses gambar produk yang memang publik.

## Batas verifikasi
- Build Vite dan browser end-to-end belum dijalankan saat pembuatan patch.
- Endpoint media memilih private disk lebih dahulu, lalu fallback public untuk foto lama.
- Form booking belum meminta upload foto; foto kondisi dapat ditambahkan Admin saat penerimaan/inspeksi.
- Tidak ada migration dan tidak ada perubahan .env.
