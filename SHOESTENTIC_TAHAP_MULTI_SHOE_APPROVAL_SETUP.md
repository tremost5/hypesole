# SHOESTENTIC – Multi-Shoe Inspection & Customer Approval

Patch ini melanjutkan kode dari ZIP `shoestentic(6).zip`.

## Isi perubahan
- Migration baru `2026_11_03_000009_add_customer_quote_approval_to_shoe_care_items.php` menambah `quoted_price`, `inspection_notes`, `customer_approval`, dan `customer_approved_at` pada `shoe_care_order_items`.
- Admin dapat menyimpan harga final dan catatan inspeksi per sepatu, lalu mengirim penawaran untuk persetujuan pelanggan.
- Pelanggan yang memiliki order dapat menyetujui atau menolak harga setiap item secara terpisah.
- Status item tidak dapat dilanjutkan ke antrean/pengerjaan/selesai/penyerahan sebelum harga disetujui.
- Data dan tabel lama tidak dihapus. Migration hanya menambah kolom.

## Cara pasang
1. Backup folder project dan database terlebih dahulu.
2. Extract ZIP lalu merge folder `app`, `database`, `resources`, dan `routes` ke project.
3. Jangan timpa `.env`.
4. Jalankan:

```bash
php artisan optimize:clear
php artisan migrate
php artisan view:clear
```

Tidak perlu menjalankan `db:seed`, `migrate:fresh`, atau `db:wipe`.

## Tes manual
1. Buka detail order Shoe Care dengan beberapa item.
2. Admin isi harga final dan catatan inspeksi untuk salah satu sepatu, lalu klik KIRIM PENAWARAN.
3. Login sebagai customer pemilik order, buka detail order, lalu setujui atau tolak penawaran item tersebut.
4. Pastikan item yang disetujui bisa dilanjutkan ke antrean/pengerjaan.
5. Pastikan item lain tidak ikut berubah.

## Pemeriksaan
PHP syntax lint lulus untuk controller, model, migration, dan routes yang diubah. Pengujian browser dan database perlu dijalankan di XAMPP lokal.
