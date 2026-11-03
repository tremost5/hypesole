# HYPESOLE — Demo Showcase Data Patch

Patch ini mengisi landing page dengan data contoh agar katalog dan galeri tidak kosong saat review UI/UX.

## Isi patch
- 5 produk sneakers baru dan 5 produk preloved.
- 5 akun pelanggan demo (login pelanggan tetap menggunakan OTP; nomor ini hanya data contoh dan bukan nomor aktif).
- 5 order Shoe Care demo berstatus `delivered`, masing-masing dengan 1 item.
- 10 ilustrasi produk SVG dan 5 ilustrasi galeri SVG lokal.
- Section galeri hasil perawatan pada landing page.

## Cara pasang
1. Backup project seperti biasa.
2. Extract isi ZIP ke root project Laravel, pilih **merge/replace files**.
3. Jalankan seeder khusus berikut dari folder project:

```bash
php artisan db:seed --class=DemoShowcaseSeeder
php artisan optimize:clear
```

4. Buka ulang landing page dan hard refresh browser.

## Keamanan data
- Seeder memakai `updateOrCreate` dengan SKU, nomor WhatsApp, dan nomor order khusus demo.
- Seeder tidak menghapus data dan tidak mengubah transaksi yang tidak memakai identitas demo.
- Tidak ada migration, perubahan `.env`, `migrate:fresh`, `db:wipe`, DROP, atau TRUNCATE.
- Menjalankan seeder berulang kali tidak membuat duplikat untuk record demo tersebut.

## Catatan
- Ilustrasi SVG adalah visual demo lokal, bukan foto asli sepatu atau dokumentasi pelanggan.
- Akun pelanggan demo tidak bisa menerima OTP karena nomor teleponnya nomor dummy.
- Lima order galeri diberi nomor `DEMO-HY-0001` sampai `DEMO-HY-0005` dan catatan internal DEMO.
