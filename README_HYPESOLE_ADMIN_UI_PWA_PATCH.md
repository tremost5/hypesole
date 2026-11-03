# HYPESOLE Admin UI + PWA polish

Patch ini ditujukan untuk ZIP `shoestentic(20261103-174941).zip`.

## Perubahan
- AdminLTE layout diperbaiki: ukuran teks lebih normal, kontras sidebar dan konten, spacing lebih rapi.
- Dashboard diganti ke komponen Bootstrap agar tidak mencampur utility Tailwind yang tidak ter-compile di layout lama.
- Login internal dirapikan dan menampilkan identitas Superadmin, Admin, Kasir.
- Sidebar memakai mekanisme responsive AdminLTE: toggle collapse desktop dan drawer overlay di layar kecil.
- Manifest dan service worker khusus admin di `public/admin/`; scope hanya `/admin/`, terpisah dari PWA public.
- Tombol install admin muncul saat browser mendukung beforeinstallprompt. Di iOS, gunakan Share > Add to Home Screen.

## Pemasangan
1. Backup folder project.
2. Extract isi ZIP ke root project dan merge/replace file yang diminta.
3. Jalankan `npm run build`.
4. Jalankan `php artisan optimize:clear`.
5. Buka `/admin`, lalu hard refresh.
6. Untuk install PWA, buka `/admin` dari Chrome/Edge, gunakan tombol Install Admin atau menu browser.

Tidak ada perubahan database, migration, `.env`, route, controller, atau data transaksi.
Admin PWA membutuhkan HTTPS saat di hosting; localhost diperbolehkan untuk development.
AdminLTE dan Bootstrap masih memakai CDN, jadi UI membutuhkan koneksi internet untuk memuat library tersebut.
