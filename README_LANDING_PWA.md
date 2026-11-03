# SHOESTENTIC Landing Page + PWA

Patch ini mengganti halaman katalog publik menjadi landing page responsive dan menambahkan fondasi PWA ringan.

## Isi patch
- `resources/views/storefront.blade.php`: landing page publik responsif, tetap memakai data produk/kategori dari controller dan route lama.
- `resources/js/app.js`: registrasi service worker dan prompt instalasi.
- `public/manifest.webmanifest`: identitas PWA.
- `public/service-worker.js`: cache shell ringan dan fallback offline.
- `public/offline.html`: halaman saat offline.
- `public/icons/*`: ikon PWA 192/512/maskable.

## Pemasangan
1. Backup folder `resources` dan `public`.
2. Merge folder `resources` dan `public` dari ZIP ke root project Laravel.
3. Jangan menimpa `.env`, database, `routes/web.php`, atau file controller.
4. Jalankan dari root project:

```bash
npm install
npm run build
php artisan optimize:clear
```

5. Buka landing page dengan HTTPS pada domain live, misalnya `https://malang-web.site` (atau domain SHOESTENTIC yang diarahkan ke aplikasi).
6. Di Chrome Android, buka menu browser lalu pilih **Install app** atau **Tambahkan ke layar utama**. Jika prompt otomatis muncul, tekan tombol **TAMBAHKAN KE LAYAR UTAMA**.

## Catatan PWA penting
- PWA bukan APK. Instalasi hanya menyimpan shortcut dan shell web ringan; tidak memasang aplikasi Android besar.
- Service worker dan install prompt memerlukan HTTPS pada hosting. `localhost` juga dianggap secure untuk pengujian, tetapi akses melalui IP LAN biasa tidak.
- Pada iPhone/iPad, gunakan Safari > Share > Add to Home Screen; browser iOS tidak selalu memunculkan prompt otomatis.
- Cache sengaja tidak menyimpan halaman admin, customer, kurir, checkout, keranjang, dan pesanan agar data pribadi tidak tersimpan di cache offline.
- Offline fallback tidak membuat transaksi dapat dilakukan offline. Login, checkout, pembayaran, tracking terbaru, dan data dinamis tetap membutuhkan internet.
- Manifest dan service worker memakai path relatif agar mendukung root domain dan instalasi lokal di subfolder seperti `/shoestentic/public/`.
- Gambar hero/layanan menggunakan gambar remote Unsplash sehingga memerlukan internet; gambar produk tetap berasal dari storage project.
