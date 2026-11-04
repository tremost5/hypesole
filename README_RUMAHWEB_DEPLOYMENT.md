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

### PWA assets forbidden (HTTP 403)

The public FTP workflow intentionally excludes `.htaccess`. If direct requests to `manifest.webmanifest`, `service-worker.js`, `offline.html`, or the PWA icons return Apache 403, merge these four rewrite rules into the existing `.htaccess` in the actual `/hypesole/` document root, immediately after `RewriteEngine On`:

```apache
RewriteRule ^manifest\.webmanifest$ index.php [L]
RewriteRule ^service-worker\.js$ index.php [L]
RewriteRule ^offline\.html$ index.php [L]
RewriteRule ^icons/(icon-192\.png|icon-512\.png|icon-512-maskable\.png)$ index.php [L]
```

Do not replace the hosting `.htaccess`; preserve its existing rules. These paths are then served by public Laravel routes with explicit MIME types, and the worker response advertises its `/hypesole/` scope. Also confirm the PWA files are present and readable by PHP in the deployed `public` tree. Validate these URLs after the change:

- `https://malang-web.site/hypesole/manifest.webmanifest`
- `https://malang-web.site/hypesole/service-worker.js`
- `https://malang-web.site/hypesole/offline.html`
- `https://malang-web.site/hypesole/icons/icon-192.png`
- `https://malang-web.site/hypesole/icons/icon-512.png`
- `https://malang-web.site/hypesole/icons/icon-512-maskable.png`

## Banner transparan

Untuk banner yang menyatu dengan background halaman, unggah file PNG atau WebP yang memang memiliki alpha transparency. JPG tidak mendukung transparansi.
