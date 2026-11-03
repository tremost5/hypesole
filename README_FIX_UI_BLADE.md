SHOESTENTIC - PATCH PERBAIKAN BLADE & UI SNEAKER STORE

Isi patch:
- Merapikan Blade detail produk dan keranjang agar directive @if/@endif tidak menempel dan mudah diaudit.
- Memperjelas tombol LIHAT & BELI pada setiap kartu produk.
- Memperjelas tombol tambah keranjang, update jumlah, hapus produk, lanjut checkout, dan buat pesanan.
- Menambahkan halaman riwayat pesanan sneaker dan detail pesanan pada lokasi view yang sesuai dengan controller.
- Menjaga warna tombol dan teks tetap kontras pada layar desktop maupun ponsel.

Cara pasang:
1. Ekstrak ZIP ini.
2. Salin folder resources ke C:\xampp\htdocs\shoestentic\resources dan pilih Merge/Replace.
3. Jangan mengganti .env, database, atau folder storage.
4. Jalankan dari folder proyek:
   php artisan view:clear
   php artisan optimize:clear
5. Muat ulang browser dengan Ctrl+F5.

Tidak ada migration atau perubahan database dalam patch ini.
