# HYPESOLE AdminLTE + CMS + Role + Fonnte

Patch ini mempertahankan database lama dan menambah satu migration untuk `site_settings` dan `banners`.

## Perubahan
- Layout admin menggunakan AdminLTE 4 + Bootstrap 5 via CDN.
- Role system: `owner` (label Superadmin), `admin`, `cashier` (Kasir), `staff`, `courier`.
- Permission khusus `website.manage`, `fonnte.manage`, `cashier.sales`.
- CRUD banner: tambah, tampil, hapus; upload gambar publik; jadwal aktif mulai/berakhir disimpan. Form edit banner belum disediakan pada patch ini.
- Fonnte: token API terenkripsi di tabel site_settings; endpoint dapat diubah; tes kirim pesan tersedia. Kosongkan token untuk mempertahankan token tersimpan.
- Banner aktif ditampilkan otomatis di homepage.

## Instalasi lokal
1. Backup folder project dan database terlebih dahulu.
2. Salin isi ZIP ke project (jangan timpa `.env`).
3. Jalankan `php artisan migrate`.
4. Jalankan `php artisan db:seed --class=RolePermissionSeeder` agar role dan permission diperbarui.
5. Jalankan `php artisan optimize:clear`.
6. Jalankan `php artisan storage:link` jika symbolic link belum ada.
7. Pastikan akun owner lama tetap memiliki role slug `owner`. Role ini ditampilkan sebagai Superadmin dan mempunyai bypass permission.

## Catatan penting
- Tidak ada migration yang menghapus atau mengubah tabel transaksi lama.
- Tidak menjalankan migrate:fresh, db:wipe, truncate, atau drop.
- Kasir diberi izin transaksi yang dibatasi; endpoint operasional lain tetap dilindungi permission.
- Role owner lama sengaja dipertahankan secara internal agar akun Superadmin yang ada tidak terkunci.
- CDN AdminLTE/Bootstrap membutuhkan koneksi internet saat membuka dashboard.
- CMS tahap ini mencakup banner promo; editor homepage lengkap untuk hero/section/galeri/testimoni merupakan tahap lanjutan.
