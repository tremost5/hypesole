# SHOESTENTIC – Modul Kurir & Pengiriman

Patch incremental untuk project Laravel SHOESTENTIC.

## Isi fitur
- Admin membuat shipment pickup atau delivery dari detail order Shoe Care.
- Metode internal (kurir toko) atau external (ekspedisi, termasuk pengiriman luar kota).
- Admin memasukkan penerima, alamat, area, jarak, ongkir, ekspedisi, dan nomor resi.
- Penugasan kurir internal dan dashboard tugas kurir.
- Kurir memperbarui status pickup, in-transit, delivered, atau failed.
- Upload foto bukti serah-terima.
- Customer melihat status, ongkir, resi, foto bukti, dan timeline event di detail order.

## Instalasi
1. Backup project dan database.
2. Merge isi ZIP ke root project. Jangan timpa `.env`.
3. Jalankan:
   ```bash
   php artisan optimize:clear
   php artisan migrate
   php artisan db:seed --class=RolePermissionSeeder
   php artisan view:clear
   ```
4. Pastikan storage link tersedia (`public/storage` -> `storage/app/public`). Jika sudah ada, jangan dihapus.
5. Login sebagai admin, buka detail order Shoe Care, klik **ATUR PICKUP / DELIVERY**.
6. Buat role/user dengan role slug `courier` (RolePermissionSeeder memastikan role courier tersedia), lalu assign kurir pada shipment.
7. Login dengan akun kurir melalui login internal yang sama. Buka `/admin/kurir`.

## Catatan penting
- Migration hanya membuat `shoe_care_shipments` dan `shoe_care_shipment_events`; tidak mengubah atau menghapus tabel lama.
- Ongkir saat ini diinput manual oleh admin. Belum ada integrasi Google Maps/routing atau API tarif ekspedisi.
- Jarak km adalah input admin dan harus diverifikasi berdasarkan rute jalan. Batas operasional 20 km belum ditegakkan otomatis karena belum ada geocoding/routing.
- Pengiriman luar kota didukung melalui mode external dengan nama ekspedisi dan nomor resi. Tracking carrier otomatis belum terhubung; event status diperbarui oleh admin.
- Satu shipment merepresentasikan satu leg perjalanan (pickup atau delivery). Untuk order yang dijemput lalu dikirim balik, buat dua shipment.
- Courier hanya dapat melihat shipment yang ditugaskan kepadanya.
- Jangan gunakan `migrate:fresh`, `db:wipe`, atau reset database.
- Footer brand: **Kharisnet : malang-web.site**.
