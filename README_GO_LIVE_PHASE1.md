# SHOESTENTIC — Go-Live Hardening Phase 1

Patch ini dibuat berdasarkan `shoestentic(10).zip` dan hanya menyentuh enam file aplikasi.

## Perubahan yang disertakan

1. Redirect guest berdasarkan URL/guard: `/customer/*` menuju `customer.login`, halaman internal menuju `admin.login`. Mengatasi fallback Laravel ke route global `login` yang tidak tersedia.
2. Endpoint setup Owner publik dihapus. Link setup Owner di halaman login juga dihapus.
3. Ditambahkan perintah CLI `php artisan shoestentic:create-owner` untuk membuat Owner pertama secara interaktif. Perintah menolak jika Owner sudah ada, meminta password dua kali, dan memakai transaksi serta lock pada record role Owner.
4. Parent order tidak dapat dipindahkan ke queued/in_progress/dan status pengerjaan lanjutan jika ada item yang belum berstatus approval `approved`.
5. Normalisasi telepon menangani prefix `0062` dan `620` sebelum prefix `0`.

## Pemasangan

Backup source terlebih dahulu dan pastikan tidak ada perubahan lokal yang belum disimpan. Ekstrak isi ZIP ke root project Laravel dan pilih **merge/replace hanya enam file yang ada dalam patch**. Jangan menimpa `.env`, database, `vendor`, atau folder `storage`.

Setelah file digabung:

```bash
php artisan optimize:clear
php artisan route:list
php artisan shoestentic:create-owner
```

Jalankan `shoestentic:create-owner` hanya jika belum ada akun Owner. Bila Owner sudah ada, perintah akan berhenti. Di hosting tanpa terminal, buat Owner pertama di lingkungan lokal/CLI yang terhubung ke database deployment secara aman; jangan membuka kembali endpoint setup publik.

## Validasi yang dilakukan pada salinan source patch

- `php -l` pada seluruh file PHP yang diubah: lulus.
- `php artisan route:list --except-vendor`: berhasil, route terdaftar.
- `php artisan test`: tidak dapat dijalankan di lingkungan pemeriksaan karena ekstensi PHP `dom`, `mbstring`, dan `xmlwriter` tidak tersedia.
- Tidak ada migration yang dibuat/dijalankan, database tidak diubah, `.env` tidak diubah.

## Belum termasuk — wajib sebelum go-live

Ini **bukan sertifikat siap produksi**. Tahap berikutnya masih harus menyelesaikan dan menguji:

- Private storage untuk foto Shoe Care/shipment beserta endpoint berotorisasi dan kompatibilitas file lama.
- Booking Shoe Care mandiri dari akun customer (form, validasi, transaksi, konfirmasi, admin workflow).
- Tes status shipment, bukti foto delivered, pembayaran, stok bersamaan, RBAC Admin/Owner, IDOR, dan actor audit log.
- Build Vite dan verifikasi PWA offline/install end-to-end.
- Pengujian browser nyata pada desktop/mobile dan konfigurasi hosting HTTPS/APP_DEBUG=false.

Jangan menghapus file lama dari `storage/app/public` atau `public/storage` pada tahap ini.
