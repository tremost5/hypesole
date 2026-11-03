# HYPESOLE Admin Dashboard 404 + PWA path fix

PENTING: Hapus folder `public/admin/` yang dibuat patch PWA sebelumnya karena folder fisik tersebut menutupi route Laravel `/admin`.

## Isi patch
- `resources/views/admin/layout.blade.php`: manifest diarahkan ke `admin-pwa`, service worker didaftarkan dengan scope `/admin/`.
- PWA assets dipindahkan ke `public/admin-pwa/` sehingga tidak ada lagi direktori fisik `public/admin/`.
- Manifest menggunakan start_url dan scope `/admin/`.

## Cara pasang
1. Extract ZIP ini ke root project, merge/replace.
2. Hapus folder lama `public/admin/` secara manual setelah memastikan isinya hanya file PWA lama (`manifest.webmanifest`, `service-worker.js`, `offline.html`). Jangan hapus folder lain.
3. Jalankan `php artisan optimize:clear`.
4. Restart `php artisan serve` (Ctrl+C lalu `php artisan serve`).
5. Buka `http://127.0.0.1:8000/admin` dengan Ctrl+F5.

## Catatan service worker
Untuk scope `/admin/` dari file worker di `/admin-pwa/`, server harus mengirim header `Service-Worker-Allowed: /admin/`. Jika memakai Apache, tambahkan file `public/admin-pwa/.htaccess` berikut atau gabungkan aturannya dengan konfigurasi server:

<IfModule mod_headers.c>
    Header set Service-Worker-Allowed "/admin/"
</IfModule>

Jika header tidak tersedia, dashboard tetap berfungsi; hanya registrasi service worker PWA Admin yang tidak aktif sampai header dikonfigurasi.
