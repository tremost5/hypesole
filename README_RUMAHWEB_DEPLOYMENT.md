# HYPESOLE – Catatan Deployment RumahWeb

## Folder penyimpanan gambar publik

Untuk deployment subfolder `https://malang-web.site/hypesole/` yang memakai FTP terpisah untuk folder aplikasi dan folder publik, atur disk `public` agar menulis langsung ke folder `storage` yang dilayani web. Tambahkan pada `.env` di server (jangan commit ke GitHub):

```dotenv
APP_URL=https://malang-web.site/hypesole
PUBLIC_STORAGE_PATH=/home/dscx7887/public_html/malang-web.site/hypesole/storage
```

Pastikan folder berikut ada dan writable oleh PHP:

- `/home/dscx7887/public_html/malang-web.site/hypesole/storage`
- `/home/dscx7887/public_html/malang-web.site/hypesole/storage/products` (dibuat otomatis saat upload bila izin folder induk memungkinkan)
- `/home/dscx7887/public_html/malang-web.site/hypesole/storage/banners`

Folder `public/storage` di hosting adalah folder publik fisik, bukan symlink Laravel. `PUBLIC_STORAGE_PATH` mengarahkan upload produk dan banner ke folder tersebut. Workflow deploy mengecualikan `public/storage/**`, sehingga file gambar yang sudah ada tidak ditimpa atau dihapus oleh deploy biasa.

Jangan mengubah `APP_KEY`, jangan menghapus `.env`, dan jangan menjalankan reset database.

## PWA

PWA menggunakan `manifest.webmanifest` dan `service-worker.js` pada subfolder `/hypesole/`. Browser tidak selalu menampilkan prompt otomatis; banner install di landing page menyediakan prompt native saat tersedia dan petunjuk pemasangan manual jika browser tidak mengirim event `beforeinstallprompt`.

## Banner transparan

Untuk banner yang menyatu dengan background halaman, unggah file PNG atau WebP yang memang memiliki alpha transparency. JPG tidak mendukung transparansi.
