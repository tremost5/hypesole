# SHOESTENTIC Full Code Audit

**Tanggal audit:** 2026-11-03  
**Cakupan:** pembacaan source/configuration, pemeriksaan migration read-only, syntax/test check, serta browser check pada localhost. Tidak ada source, `.env`, dependency, migration, atau data aplikasi yang diubah. File laporan ini adalah satu-satunya file baru.

## 1. Ringkasan Kondisi Aplikasi

Project saat ini sudah melampaui skeleton awal. Aplikasi memiliki storefront, customer login OTP melalui Fonnte, profil/alamat, sneaker cart dan checkout, admin katalog/akun/RBAC, shoe-care multi-item, approval harga per sepatu, pengiriman, dashboard kurir, serta aset PWA.

Laravel 12.70.2 dan PHP 8.2.12 berjalan pada MySQL `shoestentic`. Pemeriksaan `php artisan migrate:status` mencatat **13 migration sudah Ran**. Koneksi dan migration status diperiksa tanpa menjalankan migration.

Static route check menemukan **76 route** dengan controller dan method yang tersedia. Semua target `view()` literal ditemukan. Ada satu kegagalan runtime terkonfirmasi pada redirect guest untuk route terlindungi. Test yang tersedia lulus, tetapi cakupannya belum menguji alur autentikasi/order/authorization.

**Tidak ditemukan temuan P0.** Ada beberapa temuan P1 yang perlu ditinjau sebelum aplikasi dipakai dengan data pelanggan atau dibuka ke publik.

## 2. Daftar Error Kritis

### P1 — Guest pada route customer menghasilkan HTTP 500

Request browser ke `GET /customer/keranjang` menghasilkan `Route [login] not defined` (HTTP 500). Route berada di middleware `auth:customer`, tetapi aplikasi hanya mendaftarkan nama `customer.login` dan `admin.login`, tidak ada route bernama global `login`. Redirect bawaan middleware autentikasi Laravel mencoba nama global tersebut. Grup `auth:web` admin berpotensi mengalami masalah sama.

Bukti: [routes/web.php](routes/web.php#L63), [routes/web.php](routes/web.php#L99), [routes/web.php](routes/web.php#L48), [routes/web.php](routes/web.php#L141), [bootstrap/app.php](bootstrap/app.php#L8-L17).

### P1 — Bootstrap Owner dapat diambil alih selama belum ada Owner

Route setup Owner hanya dibatasi `guest:web`. Controller menyajikan dan menerima pembuatan Owner selama query belum menemukan akun dengan role `owner`; tidak ada token setup atau batas jaringan pada alur ini. Jika aplikasi dapat diakses pihak lain sebelum Owner pertama dibuat, pengunjung dapat mendaftarkan akun Owner. POST memiliki throttle, tetapi throttle bukan autentikasi.

Bukti: [routes/web.php](routes/web.php#L145-L152), [app/Http/Controllers/AdminAuthController.php](app/Http/Controllers/AdminAuthController.php#L62-L85).

### P1 — Foto kondisi dan bukti pengiriman disimpan pada disk public

Foto sebelum/sesudah Shoe Care dan foto bukti shipment disimpan menggunakan disk `public`; Blade membentuk URL langsung melalui `/storage/...`. `public/storage` tersedia pada pemeriksaan lokal. URL file publik tidak melewati pemeriksaan kepemilikan order, sehingga foto sepatu pelanggan dapat diakses siapa pun yang memperoleh URL.

Bukti: [app/Http/Controllers/AdminShoeCareOrderController.php](app/Http/Controllers/AdminShoeCareOrderController.php#L133-L149), [app/Http/Controllers/AdminShoeCareOrderController.php](app/Http/Controllers/AdminShoeCareOrderController.php#L252-L256), [app/Http/Controllers/AdminShipmentController.php](app/Http/Controllers/AdminShipmentController.php#L96-L110), [resources/views/customer/orders/show.blade.php](resources/views/customer/orders/show.blade.php#L34-L48), [config/filesystems.php](config/filesystems.php#L28-L46).

## 3. Bug Fungsional

### P1 — CTA booking customer tidak memiliki endpoint pembuatan order

CTA “BOOK SHOE CARE” mengarah ke login. Setelah login, route customer untuk Shoe Care hanya menampilkan daftar/detail order dan approval item; tidak ada POST customer untuk membuat order. Pembuatan Shoe Care saat ini tersedia melalui admin/staff. Akibatnya pelanggan tidak dapat menyelesaikan booking yang dijanjikan storefront.

Bukti: [resources/views/storefront.blade.php](resources/views/storefront.blade.php#L55-L56), [routes/web.php](routes/web.php#L85-L93), [routes/web.php](routes/web.php#L198-L214).

### P1 — Status parent order dapat melewati approval harga item

Endpoint `updateStatus` menerima status parent termasuk `queued`, `in_progress`, dan seterusnya tanpa memeriksa `customer_approval`. Pemeriksaan persetujuan hanya dilakukan pada endpoint status item. Parent dapat ditandai sedang dikerjakan walaupun item masih pending/rejected, sehingga status pelanggan dapat bertentangan dengan kontrol persetujuan per sepatu.

Bukti: [app/Http/Controllers/AdminShoeCareOrderController.php](app/Http/Controllers/AdminShoeCareOrderController.php#L182-L223), [app/Http/Controllers/AdminShoeCareOrderController.php](app/Http/Controllers/AdminShoeCareOrderController.php#L233-L247).

### P2 — Status order/item/shipment tidak memiliki state transition yang ketat

Endpoint admin menerima status Shoe Care dari daftar penuh pada setiap update; shipment admin juga menerima setiap status yang diizinkan tanpa memvalidasi status asal. Item dan parent dapat berbeda karena parent hanya diselaraskan ketika semua item memiliki status sama. Perlu aturan transisi terpusat, termasuk perilaku cancel/delivered dan agregasi status multi-item.

Bukti: [app/Http/Controllers/AdminShoeCareOrderController.php](app/Http/Controllers/AdminShoeCareOrderController.php#L182-L221), [app/Http/Controllers/AdminShoeCareOrderController.php](app/Http/Controllers/AdminShoeCareOrderController.php#L258-L278), [app/Http/Controllers/AdminShipmentController.php](app/Http/Controllers/AdminShipmentController.php#L84-L110).

### P2 — Normalisasi nomor pada order Shoe Care salah untuk prefix `0062`

`normalizeIndonesianPhone()` menangani semua nomor yang diawali `0` sebelum memeriksa `620`. Input `0062...` karena itu diubah menjadi `62062...`, tidak konsisten dengan normalisasi OTP/customer yang mendukung `0062`. Pencocokan akun customer untuk order yang dibuat staf dapat gagal.

Bukti: [app/Http/Controllers/AdminShoeCareOrderController.php](app/Http/Controllers/AdminShoeCareOrderController.php#L301-L317).

### P2 — Bukti foto kurir tidak diwajibkan saat status delivered

Validasi `proof_photo` bersifat nullable dan tidak ada syarat kondisional ketika status menjadi `delivered`. Form kurir juga tidak menandai input foto sebagai required. Pengiriman dapat ditandai terkirim tanpa bukti yang disebutkan pada UI.

Bukti: [app/Http/Controllers/AdminShipmentController.php](app/Http/Controllers/AdminShipmentController.php#L84-L110), [app/Http/Controllers/CourierController.php](app/Http/Controllers/CourierController.php#L20-L31), [resources/views/courier/dashboard.blade.php](resources/views/courier/dashboard.blade.php#L9-L13).

### P2 — Perubahan status shipment dan event tidak atomik

`applyStatus()` menyimpan foto, memperbarui shipment, lalu membuat event tanpa transaksi database. Jika pembuatan event gagal, status utama dapat berubah tanpa riwayat; kegagalan DB setelah penyimpanan foto juga dapat meninggalkan file tanpa referensi.

Bukti: [app/Http/Controllers/AdminShipmentController.php](app/Http/Controllers/AdminShipmentController.php#L96-L110).

### P2 — Status pembayaran tidak dikaitkan dengan transisi status order

Admin dapat mengirim kombinasi status pesanan dan `payment_status` secara bebas. Validasi menerima nilai masing-masing, tetapi tidak menegakkan aturan seperti pesanan selesai harus dibayar atau refund terkait pembatalan/refund nyata. `completed_at` juga ditulis ulang setiap update ketika status tetap `completed`.

Bukti: [app/Http/Controllers/AdminStoreOrderController.php](app/Http/Controllers/AdminStoreOrderController.php#L22-L56).

### P2 — Proteksi default address rentan kondisi konkuren

Controller menghapus flag alamat default lalu mengatur alamat baru dalam transaksi, tetapi tidak mengunci daftar alamat customer. Dua request konkuren berpotensi menghasilkan lebih dari satu default; schema hanya memiliki index, bukan constraint unik yang menegakkan satu default per customer.

Bukti: [app/Http/Controllers/CustomerAccountController.php](app/Http/Controllers/CustomerAccountController.php#L39-L64), [database/migrations/2026_11_03_000003_create_customer_addresses_table.php](database/migrations/2026_11_03_000003_create_customer_addresses_table.php#L10-L23).

## 4. Temuan Keamanan

### P1 — Role Admin dan Owner mendapat seluruh permission

Seeder memberikan `array_keys($permissions)` kepada Owner dan Admin. `hasPermission()` juga memberi Owner akses penuh. Dengan definisi saat ini Admin dapat mengelola akun dan role sebagaimana Owner; separation of duties yang biasanya diharapkan dari dua role tersebut tidak ada.

Bukti: [database/seeders/RolePermissionSeeder.php](database/seeders/RolePermissionSeeder.php#L35-L41), [app/Models/User.php](app/Models/User.php#L28-L38).

### P2 — Actor customer tidak tercatat pada riwayat approval

Ketika customer menyetujui/menolak harga, status log dibuat dengan `user_id = null`. Jejak audit tidak mengidentifikasi customer yang melakukan keputusan; schema log juga hanya menunjuk tabel `users`, bukan `customers`.

Bukti: [app/Http/Controllers/CustomerOrderController.php](app/Http/Controllers/CustomerOrderController.php#L23-L41), [database/migrations/2026_11_03_000006_create_shoe_care_orders_tables.php](database/migrations/2026_11_03_000006_create_shoe_care_orders_tables.php#L35-L43).

### P2 — Debug mode menampilkan detail exception

Konfigurasi development memakai `APP_DEBUG=true`. Browser request yang menghasilkan 500 menampilkan halaman exception lengkap, path framework, versi, dan trace. Ini sesuai development lokal, tetapi berisiko membocorkan detail aplikasi bila konfigurasi tersebut terbawa ke server yang dapat diakses publik.

Bukti runtime: request guest `/customer/keranjang`; konfigurasi aktif berasal dari `.env` (tidak diubah dalam audit).

**Kontrol yang terkonfirmasi:** OTP di-hash, masa berlaku 5 menit, dibatasi 5 percobaan per OTP, digunakan sekali, nomor Indonesia dinormalisasi pada login, dan ada throttle route/controller. Fonnte memvalidasi status sukses provider dan tidak mencatat kode/token. Customer order/address serta shipment kurir memiliki pemeriksaan kepemilikan. Form POST yang diperiksa menggunakan CSRF. Tidak ditemukan raw Blade echo pada view aktif atau query SQL mentah dari input pengguna pada pembacaan source ini.

## 5. Temuan UI/UX dan Responsive

- Pada viewport 375 px, browser mengukur `clientWidth=375` dan `scrollWidth=377` (**overflow horizontal 2 px**). Elemen penyebab spesifik belum berhasil diisolasi.
- Navigasi anchor storefront memakai `hidden ... md:flex`, tanpa menu alternatif mobile; navigasi “Layanan”, “Sneaker Shop”, dan “Cara Kerja” tidak tampil pada layar kecil.
- Produk di homepage saat ini 0; empty state ditampilkan dengan benar. Konten produk tetap bergantung pada data katalog.
- Foto hero termuat pada browser check; gambar berasal dari Unsplash dan memerlukan koneksi eksternal.

Bukti: [resources/views/storefront.blade.php](resources/views/storefront.blade.php#L37-L38), [resources/views/storefront.blade.php](resources/views/storefront.blade.php#L103-L120).

## 6. Temuan PWA

- `manifest.webmanifest`, icon 192/512/maskable, `service-worker.js`, dan `offline.html` tersedia.
- Browser check pada localhost mengembalikan HTTP 200 untuk manifest/service worker, melaporkan service worker terdaftar, dan gambar hero berhasil dimuat.
- Service worker mengecualikan route admin/customer/courier/checkout/cart/product dari cache; form/non-GET tidak dicegat. Aset build/icon disimpan dalam cache dan tersedia offline fallback.
- **Offline fallback tidak berhasil diverifikasi end-to-end**: simulasi offline pada browser tidak menghasilkan respons final. PWA install prompt juga hanya diuji melalui pembacaan source, bukan instalasi browser nyata.

Bukti: [resources/js/app.js](resources/js/app.js#L3-L18), [public/service-worker.js](public/service-worker.js#L1-L28), [public/manifest.webmanifest](public/manifest.webmanifest#L1-L35), [public/offline.html](public/offline.html).

## 7. Hasil Pengujian yang Benar-benar Dijalankan

| Pemeriksaan | Hasil |
|---|---|
| `php artisan route:list` | Lulus; 76 route terdaftar. |
| Static route/controller/view check | Semua controller method dan target view literal ditemukan. Referensi `login`/`register` yang tidak ada hanya ditemukan pada `welcome.blade.php`, bukan halaman aktif. |
| `php artisan migrate:status` | Lulus read-only; 13 migration tercatat `Ran`. Tidak ada migration dijalankan. |
| `php -l` | Lulus untuk 76 file PHP project. |
| `php artisan test --testsuite=Unit --do-not-cache-result` | 4 test, 6 assertion lulus. |
| `php artisan test --testsuite=Feature --do-not-cache-result` | 1 test, 1 assertion lulus. |
| Browser `GET /customer/keranjang` sebagai guest | Gagal HTTP 500; exception `Route [login] not defined`. |
| Browser homepage/PWA | Homepage 200; manifest dan service worker 200; service worker registered; hero image loaded. |
| Responsive viewport 375 px | Overflow terukur 2 px (375 px vs 377 px). |
| `npm run build` | **Belum terverifikasi.** Perintah build ke direktori sementara tidak selesai dieksekusi karena terminal PowerShell masuk prompt continuation setelah kesalahan quoting. Tidak ada klaim build lulus dan tidak ada dependency yang diinstal. |
| Foreign-key metadata query langsung | **Belum dijalankan.** Relasi dan constraint ditinjau dari migration/model; status migration saja bukan verifikasi metadata FK runtime. |

Tidak ada test yang menggunakan `RefreshDatabase`; test feature yang berjalan hanya menguji HTTP 200 homepage. Belum ada test untuk OTP, cart/checkout, stock race, status transition, approval, role permission, IDOR, upload, atau shipment.

## 8. File Relevan dan Lokasi Temuan

- Routes/auth redirect: [routes/web.php](routes/web.php#L45-L63), [routes/web.php](routes/web.php#L89-L102), [routes/web.php](routes/web.php#L141-L156).
- Owner bootstrap: [app/Http/Controllers/AdminAuthController.php](app/Http/Controllers/AdminAuthController.php#L62-L85).
- OTP/provider: [app/Http/Controllers/CustomerAuthController.php](app/Http/Controllers/CustomerAuthController.php#L16-L21), [app/Http/Controllers/CustomerAuthController.php](app/Http/Controllers/CustomerAuthController.php#L46-L109), [app/Http/Controllers/CustomerAuthController.php](app/Http/Controllers/CustomerAuthController.php#L122-L215), [app/Services/FonnteService.php](app/Services/FonnteService.php#L12-L51).
- Customer ownership/address/cart/checkout: [app/Http/Controllers/CustomerAccountController.php](app/Http/Controllers/CustomerAccountController.php#L39-L127), [app/Http/Controllers/CustomerOrderController.php](app/Http/Controllers/CustomerOrderController.php#L12-L53), [app/Http/Controllers/CustomerStoreOrderController.php](app/Http/Controllers/CustomerStoreOrderController.php#L15-L86).
- Shoe Care, item approval, photos, status: [app/Http/Controllers/AdminShoeCareOrderController.php](app/Http/Controllers/AdminShoeCareOrderController.php#L64-L160), [app/Http/Controllers/AdminShoeCareOrderController.php](app/Http/Controllers/AdminShoeCareOrderController.php#L182-L320), [app/Http/Controllers/CustomerOrderController.php](app/Http/Controllers/CustomerOrderController.php#L23-L41).
- Shop, inventory, payments: [app/Http/Controllers/CustomerCartController.php](app/Http/Controllers/CustomerCartController.php#L8-L51), [app/Http/Controllers/CustomerStoreOrderController.php](app/Http/Controllers/CustomerStoreOrderController.php#L15-L86), [app/Http/Controllers/AdminStoreOrderController.php](app/Http/Controllers/AdminStoreOrderController.php#L20-L61), [app/Http/Controllers/AdminProductController.php](app/Http/Controllers/AdminProductController.php#L55-L127).
- RBAC/courier/shipment: [app/Http/Middleware/EnsurePermission.php](app/Http/Middleware/EnsurePermission.php#L8-L17), [app/Models/User.php](app/Models/User.php#L28-L38), [database/seeders/RolePermissionSeeder.php](database/seeders/RolePermissionSeeder.php#L35-L41), [app/Http/Controllers/AdminShipmentController.php](app/Http/Controllers/AdminShipmentController.php#L84-L110), [app/Http/Controllers/CourierController.php](app/Http/Controllers/CourierController.php#L16-L31).
- Schema/relation: `database/migrations/0001_01_01_000000_create_users_table.php`, `database/migrations/2026_11_03_000001_create_customers_table.php` through `database/migrations/2026_11_04_000001_create_shoe_care_shipments_tables.php`; models reside in `app/Models/`.
- Storage/PWA/frontend: [config/filesystems.php](config/filesystems.php#L18-L59), [resources/views/storefront.blade.php](resources/views/storefront.blade.php#L1-L135), [resources/js/app.js](resources/js/app.js#L1-L43), [public/service-worker.js](public/service-worker.js#L1-L29), [public/manifest.webmanifest](public/manifest.webmanifest#L1-L35).
- Automated tests: [tests/Feature/ExampleTest.php](tests/Feature/ExampleTest.php), [tests/Unit/FonnteServiceTest.php](tests/Unit/FonnteServiceTest.php), [tests/Unit/ExampleTest.php](tests/Unit/ExampleTest.php).

## 9. Rekomendasi Prioritas

### P0 — Critical

Tidak ada temuan P0 terkonfirmasi dalam audit ini.

### P1 — High

1. Atur redirect guest untuk masing-masing guard agar mengarah ke login yang tepat; tambahkan test untuk route customer dan admin tanpa autentikasi.
2. Kunci setup Owner dengan prosedur bootstrap terbatas (misalnya hanya local/CLI atau secret satu kali), termasuk perlindungan terhadap request konkuren.
3. Simpan foto kondisi/proof pada private disk dan sajikan melalui endpoint berotorisasi, bukan URL storage publik.
4. Tambahkan jalur booking Shoe Care untuk customer atau ubah CTA/copy bila booking memang hanya diterima staff.
5. Terapkan aturan approval quote pada state machine order/item, bukan hanya pada satu endpoint status item.

### P2 — Medium

1. Definisikan state transition untuk Shoe Care dan shipment; satukan aggregate status parent/item dan catatan actor.
2. Wajibkan proof foto pada status delivery sesuai kebijakan, dan simpan update shipment + event secara atomik.
3. Samakan normalisasi nomor telepon di semua alur; tambahkan test untuk `08...`, `62...`, `+62...`, dan `0062...`.
4. Tetapkan invariant payment/status dan uji pengurangan/pengembalian stok dengan transaksi konkuren.
5. Putuskan pemisahan permission Owner/Admin; tambahkan authorization test untuk Staff/Courier dan akses URL langsung.
6. Tambahkan constraint/strategi konsistensi untuk alamat default dan uji request konkuren.
7. Perbaiki navigasi mobile dan investigasi overflow 2 px pada viewport 375 px.
8. Jalankan serta catat Vite production build dan uji offline fallback/install PWA pada browser.

### P3 — Low

1. Hapus atau perbarui view welcome yang tidak digunakan dan masih merujuk ke route `login`/`register` yang tidak ada.
2. Tambahkan test regression untuk alur dan batas akses sebelum memperluas fitur.

---

Audit selesai tanpa implementasi atau perbaikan. Temuan di atas perlu ditinjau sebelum perubahan kode dilakukan.