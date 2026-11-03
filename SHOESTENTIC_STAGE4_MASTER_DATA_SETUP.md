# SHOESTENTIC — Tahap 4: Master Data

## Fitur
- Kategori Shoe Care dan kategori Sneaker Store.
- Master layanan/treatment, harga, estimasi durasi, foto, status aktif.
- Master produk sneaker baru/preloved, SKU, brand, ukuran, kondisi, harga, harga modal internal, foto, status aktif.
- Stok awal otomatis tercatat sebagai inventaris masuk.
- Penyesuaian stok masuk/keluar/set stok akhir dengan transaksi database dan penguncian baris.
- Riwayat pergerakan stok dan petugas.
- Produk dinonaktifkan/diarsipkan tanpa menghapus riwayat stok.

## Instalasi
1. Backup folder project.
2. Merge isi ZIP ke root project, jangan timpa `.env`.
3. Jalankan:

```bash
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan storage:link
php artisan route:list --path=admin
```

Migration menambahkan tabel baru `service_categories`, `services`, `product_categories`, `products`, `inventory_movements`. Tidak menghapus tabel atau data lama.

Seeder menambahkan permission master data dan memberi akses ke Owner serta Admin. Staff/Courier tetap hanya memiliki permission yang sudah diberikan; Owner selalu memiliki akses penuh.

## Menu
- `/admin/catalog` — kategori layanan dan produk.
- `/admin/services` — daftar treatment Shoe Care.
- `/admin/products` — katalog Sneaker Store dan stok.
