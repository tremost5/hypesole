# HYPESOLE PWA Go-Live Report

**Tanggal:** 2026-11-03  
**Environment test:** Laravel 12.70.2, PHP 8.2.12, Vite 6.4.5, browser localhost loopback.  
**Safety:** `.env`, database, migration, transaction data, and existing photos were not changed. No migration, database CLI, or package installation was run.

## Ringkasan

PWA HYPESOLE telah diperbaiki dan dibangun. Production Vite build berhasil, manifest dan tiga ikon valid, service worker terdaftar pada scope root dan aktif, versi cache lama diganti oleh `hypesole-pwa-v4`, serta halaman offline dan alur prompt install diuji di browser lokal. Cache hanya berisi offline page, manifest, icon, dan CSS/JS Vite; navigasi/HTML dan data pelanggan tidak disimpan.

## Penyebab PWA Sebelumnya Belum Siap

1. Manifest hanya disertakan pada landing page; halaman detail produk publik belum memilikinya.
2. Service worker lama hanya menyimpan offline page dan icon. Aset CSS/JS Vite tidak dicache sehingga shell offline tidak lengkap.
3. Halaman utama tidak diprecache. Navigasi fallback dapat gagal jika URL halaman belum pernah dicache.
4. Identitas offline masih menyebut SHOESTENTIC, tidak konsisten dengan HYPESOLE.
5. Cache namespace lama mencampur brand lama dan baru, sehingga cleanup lintas versi tidak dapat diandalkan.
6. Banner install sebelumnya bergantung pada `beforeinstallprompt`; tidak ada instruksi Add to Home Screen untuk iPhone/Safari.
7. Navigasi utama memakai `hidden` pada mobile breakpoint sehingga hilang pada layar kecil.
8. Percobaan browser lokal menunjukkan registrasi install/network precache tertahan pada single-worker server. Worker kini tidak melakukan network fetch dalam install event; precache inti dilakukan berurutan pada activation.

## File yang Diperbaiki

- [public/manifest.webmanifest](public/manifest.webmanifest): identitas, id, scope/start URL relatif, warna navy, dan ikon.
- [public/service-worker.js](public/service-worker.js): scope relatif, cache versi v4, pembersihan cache HYPESOLE/brand lama, offline fallback, caching aset statis, dan pengecualian route privat.
- [public/offline.html](public/offline.html): halaman offline beridentitas HYPESOLE, responsif, aman untuk safe area.
- [resources/js/app.js](resources/js/app.js): registrasi worker hanya pada secure context, scope relatif ke manifest, install prompt, mode standalone, dan petunjuk iOS.
- [resources/views/storefront.blade.php](resources/views/storefront.blade.php): viewport-fit, install banner HYPESOLE, safe-area spacing, serta navigasi mobile.
- [resources/views/storefront/show.blade.php](resources/views/storefront/show.blade.php): manifest, Apple touch icon, app title, theme color, dan viewport-fit untuk halaman publik detail produk.
- `public/build/manifest.json` dan aset build CSS/JS dihasilkan ulang oleh Vite.

`.env`, database, routes bisnis, migration, foto, serta dependency tidak diubah. `vite.config.js` dan `package.json` dipertahankan.

## Konfigurasi yang Digunakan

- Manifest `name` dan `short_name`: `HYPESOLE`.
- Start URL dan scope: relatif (`./`) agar mengikuti root deployment atau subfolder.
- Theme/background: `#07152f`.
- Worker didaftarkan relatif ke URL manifest; scope dihitung dari direktori manifest.
- Cache: `hypesole-pwa-v4`; cache lama dengan prefix HYPESOLE/SHOESTENTIC dibersihkan saat activation.
- Cache berisi offline page, manifest, ikon, dan file CSS/JS hasil Vite. Tidak ada HTML/page response yang disimpan.
- Admin, customer, courier, checkout, cart, order, login, OTP, API, payment, storage/photo, dan semua method selain GET tidak dicache.
- Navigasi selalu mencoba jaringan; kegagalan jaringan memberikan offline page, bukan transaksi lama.
- Service worker didaftarkan hanya pada secure context. `localhost`/loopback memenuhi pengecualian secure-context browser untuk development; production harus HTTPS.

Ikon lama sudah sesuai identitas HYPESOLE: monogram H navy. Tidak dibuat atau ditimpa file ikon.

## Hasil Pengujian

| Pemeriksaan | Hasil |
|---|---|
| `npm run build` | **Lulus.** Vite 6.4.5 memproses 61 module; menghasilkan manifest dan aset CSS/JS hash. |
| Manifest JSON | **Lulus.** Browser berhasil parse manifest; name/short_name HYPESOLE, start URL/scope relatif, theme/background navy. |
| Icon 192 | **Lulus.** HTTP 200, MIME image/png, terdecode 192×192. |
| Icon 512 | **Lulus.** HTTP 200, MIME image/png, terdecode 512×512. |
| Icon 512 maskable | **Lulus.** HTTP 200, MIME image/png, terdecode 512×512, purpose maskable. |
| HTTP asset status | **Lulus.** Manifest, service worker, offline page, dan ketiga ikon semuanya HTTP 200. |
| Secure context | **Lulus untuk development.** Browser loopback melaporkan `isSecureContext=true`. Ini bukan verifikasi sertifikat HTTPS production. |
| Service worker | **Lulus pada `127.0.0.1:8000`.** Active `activated`, controller aktif, scope `http://127.0.0.1:8000/`. |
| Cache versi | **Lulus.** Hanya `hypesole-pwa-v4` terlihat setelah update; cache tersebut memuat offline page, manifest, icons, CSS, JS. |
| Offline navigation | **Lulus pada browser simulation.** Saat jaringan dimatikan dan URL navigasi tidak tersedia, browser menerima HTTP 200 dengan title `Offline | HYPESOLE` dan pesan offline. |
| Cache transaksi/pribadi | **Lulus pemeriksaan cache.** Tidak ada halaman customer/order/admin, response HTML, atau foto di daftar cache; hanya asset statis yang diminta. POST/route privat dilewatkan worker. |
| Install prompt UI | **Lulus simulasi browser.** `beforeinstallprompt` dicegah, banner muncul dengan tombol `INSTALL HYPESOLE`, tombol memanggil `prompt()` sekali, dan banner hilang setelah hasil accepted. |
| Standalone event | Kode memeriksa `display-mode: standalone` dan `navigator.standalone`; pemasangan aktual pada OS belum dilakukan. |
| Responsive | **Lulus browser check 375/390 px.** Lebar dokumen sama dengan viewport; horizontal overflow tidak ditemukan. Navigasi mobile tampil dan viewport memakai `viewport-fit=cover`. |
| JavaScript console | Tidak ada exception JavaScript PWA pada registrasi loopback yang diuji. Browser harness melaporkan satu gambar Unsplash diblokir (`ERR_BLOCKED_BY_ORB`); ini error resource eksternal, bukan worker/install script. |
| `php artisan test` | **Lulus.** 5 test, 7 assertions. |
| iPhone/Safari nyata | **Belum diuji pada perangkat.** Instruksi Add to Home Screen tersedia dalam banner khusus iOS. |
| Android/Desktop native install | Event prompt diuji secara simulasi, bukan dialog native pada Android/Chrome/Edge perangkat nyata. |
| Subfolder hosting | Path dibangun relatif dan root diuji; hosting subfolder belum tersedia untuk pengujian end-to-end. |

## HTTPS untuk Hosting

Service worker dan install PWA production mensyaratkan secure context. Pasang sertifikat TLS yang valid pada domain hosting (misalnya sertifikat ACME/Let's Encrypt melalui panel hosting), aktifkan redirect HTTP ke HTTPS, lalu pastikan URL final manifest, service worker, icon, dan Vite asset semuanya memakai HTTPS. Jangan mengubah `.env` untuk audit ini. Setelah deploy, buka situs melalui HTTPS dan periksa DevTools → Application → Manifest/Service Workers serta Console.

`localhost` dan `127.0.0.1` hanya merupakan pengecualian secure context untuk development; keduanya bukan target konfigurasi production.

## Instruksi Instalasi

### Android

1. Buka situs HYPESOLE melalui Chrome dan HTTPS.
2. Tunggu tombol **INSTALL HYPESOLE** muncul, lalu ketuk; atau buka menu Chrome dan pilih **Install app** / **Add to Home screen**.
3. Konfirmasi pemasangan. Ikon HYPESOLE akan tersedia pada launcher.

### iPhone/iPad

1. Buka situs melalui Safari dan HTTPS.
2. Ketuk tombol Bagikan.
3. Pilih **Add to Home Screen**, lalu ketuk **Add**.

iOS Safari tidak menyediakan `beforeinstallprompt`; banner memberikan instruksi manual tersebut.

### Desktop Chrome/Edge

1. Buka HYPESOLE melalui HTTPS.
2. Klik tombol **INSTALL HYPESOLE** saat browser menyediakan install prompt, atau gunakan ikon install pada address bar/menu browser.
3. Konfirmasi pemasangan.

## Verifikasi Manual yang Masih Diperlukan

1. Uji fresh install, upgrade versi, dan offline launch pada Android Chrome dan desktop Chrome/Edge.
2. Uji **Add to Home Screen** dan standalone launch pada iPhone/iPad Safari.
3. Deploy sementara pada URL subfolder sungguhan untuk memeriksa path rewriting web server.
4. Uji HTTPS production dengan sertifikat/domain final dan pastikan semua asset production dapat diakses.
5. Pertimbangkan self-host hero/service images bila gambar eksternal perlu tersedia offline atau konsol production harus bebas dari error resource pihak ketiga.

PWA meng-cache app shell statis saja. Halaman katalog/navigasi dinamis tetap memerlukan koneksi; offline mode tidak menampilkan snapshot transaksi atau data pengguna.