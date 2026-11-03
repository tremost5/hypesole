# HYPESOLE

Laravel project source snapshot.

## Setup lokal
1. `composer install`
2. Salin `.env.example` menjadi `.env`
3. Isi konfigurasi database dan Fonnte pada `.env` lokal (jangan commit `.env`)
4. `php artisan key:generate`
5. `php artisan migrate --seed` hanya untuk database baru/kosong. Untuk database yang sudah berisi data, jalankan migration sesuai kebutuhan dan jangan gunakan `migrate:fresh`.
6. `npm install`
7. `npm run build`
8. `php artisan storage:link`
9. `php artisan serve`

## Catatan repository
- `vendor`, `node_modules`, `public/build`, file database SQLite, log, session, upload pelanggan, dan data runtime tidak disertakan.
- `.env.example` sudah mengosongkan token Fonnte. Masukkan token asli hanya di `.env` lokal/server.
- Jangan pernah commit `.env`, token API, foto pelanggan, atau data transaksi.
