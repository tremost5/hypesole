# SHOESTENTIC — Tahap 5: Transaksi Shoe Care

## Fitur
- Nomor order otomatis `SC-YYMMDD-XXXXX`.
- Penerimaan order oleh Owner/Admin/Staff.
- Customer terdaftar atau walk-in; data nama/WhatsApp tersimpan sebagai snapshot.
- Pilihan layanan aktif dari master data Tahap 4, harga layanan disalin ke order.
- Detail sepatu, keluhan, catatan customer/internal.
- Foto kondisi awal dan foto proses/hasil (maksimal 5 file per unggahan; JPG/PNG/WEBP, 4 MB/file).
- Estimasi selesai dan penugasan staff.
- Status order dan riwayat perubahan.
- Customer dapat melihat order yang terhubung dengan Customer ID akun mereka.

## Cara pasang
1. Backup project.
2. Extract ZIP dan merge folder `app`, `database`, `resources`, `routes` ke root project.
3. Jangan timpa `.env`.
4. Jalankan:
```bash
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan storage:link
php artisan route:list --path=shoe-care-orders
```
Migration baru nomor `2026_11_03_000006`. Hanya membuat tabel order dan log status; tidak mengubah atau menghapus tabel lama.

## URL
- Admin order: `/admin/shoe-care-orders`
- Buat order: `/admin/shoe-care-orders/create`
- Customer order: `/customer/orders`

## Catatan
- Login Customer tetap OTP WhatsApp; login internal tetap guard `web`.
- Staff diberi permission lihat/buat/update order. Courier belum diberi akses order pada tahap ini.
- Order tampil di akun customer jika dipilihkan customer terdaftar (nomor WhatsApp terverifikasi). Walk-in tetap tampil di panel admin.
- Jangan jalankan `migrate:fresh` atau `db:wipe`.
