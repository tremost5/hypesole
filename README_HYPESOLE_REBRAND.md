# HYPESOLE Rebrand & Visual Refresh Patch

Patch dibuat dari source `shoestentic(20261103-094938).zip`.

## Isi perubahan
- Rebrand runtime `SHOESTENTIC` menjadi `HYPESOLE` pada halaman aplikasi, OTP, manifest PWA, dan pesan UI.
- Landing page diperbarui ke identitas biru, putih, dan hitam dengan gradient navy-blue, glass surfaces, highlight biru, card depth, dan responsive spacing.
- Global focus/accent styling diarahkan ke biru.
- PWA theme/background color diperbarui dan cache shell dinaikkan ke v3 agar aset lama tidak tertahan.
- Nama Artisan command owner/media mengikuti HYPESOLE.

## Pemasangan
1. Backup folder project.
2. Ekstrak patch ke root `C:\xampp\htdocs\shoestentic` (atau root project aktif), pilih merge/replace.
3. Jangan menimpa `.env`, database, `vendor`, atau `node_modules`.
4. Jalankan:
   ```bash
   php artisan optimize:clear
   npm run build
   php artisan route:list
   php artisan test
   ```
5. Hard refresh browser / clear site data PWA bila identitas lama masih terlihat.

## Catatan
- Patch tidak mengubah database, migration, data transaksi, atau konfigurasi `.env`.
- Folder proyek di Windows boleh tetap bernama `shoestentic`; nama folder bukan nama brand yang ditampilkan.
- Ikon PWA yang ada dipertahankan. Untuk logo HYPESOLE yang benar-benar baru, siapkan logo final atau minta desain logo terpisah.
- File laporan historis/audit dan cache hasil kompilasi tidak ditimpa. `npm run build` akan menghasilkan aset build baru.
- Belum diklaim lulus browser QA; lakukan verifikasi setelah build lokal.
