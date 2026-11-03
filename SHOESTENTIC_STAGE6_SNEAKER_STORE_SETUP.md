# SHOESTENTIC Tahap 6 — Sneaker Store

## Termasuk
- Katalog publik produk aktif dan stok tersedia; filter pencarian, kategori, baru/preloved.
- Detail produk dan foto.
- Keranjang belanja berbasis session.
- Checkout dengan alamat tersimpan dan pilihan transfer/cash.
- Pesanan tersimpan dengan snapshot produk, harga, dan alamat.
- Panel admin untuk konfirmasi, proses, penyelesaian, pembatalan, dan status pembayaran.
- Stok dikurangi saat admin mengonfirmasi pesanan; pembatalan mengembalikan stok dan mencatat inventory movement.
- Riwayat pesanan customer.

## Pemasangan
1. Backup project.
2. Extract ZIP dan merge folder ke root project.
3. Jangan timpa `.env`.
4. Jalankan:
```bash
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan route:list --path=store-orders
php artisan route:list --path=keranjang
```
Migration baru: `2026_11_03_000007_create_store_orders_tables`. Tidak menghapus tabel/data lama.

## Tes
1. Pastikan produk aktif punya stok lebih dari 0 dan foto opsional.
2. Buka `/`, pilih produk, tambah ke keranjang.
3. Login customer OTP jika diminta, simpan minimal satu alamat pada dashboard customer.
4. Checkout dan buat pesanan.
5. Login admin, buka `/admin/store-orders`.
6. Ubah status dari Menunggu konfirmasi menjadi Dikonfirmasi. Stok akan berkurang dan riwayat inventaris tercatat.
7. Ubah ke Diproses, Siap diserahkan, lalu Selesai.
8. Tes pembatalan sebelum dan sesudah konfirmasi; stok dikembalikan hanya jika sebelumnya sudah dikurangi.

## Catatan penting
- Ongkir tahap ini disimpan Rp0; customer tidak dapat mengubah ongkir. Penetapan ongkir akan dibuat pada tahap pengiriman/setting berikutnya.
- Pembayaran masih pencatatan manual (Cash/Transfer), belum payment gateway.
- Produk preloved menggunakan stok kuantitas yang diatur admin. Untuk barang satuan, isi stok 1.
- Checkout memerlukan alamat tersimpan.
- Jangan jalankan `migrate:fresh` atau `db:wipe`.
