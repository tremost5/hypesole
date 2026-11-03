# SHOESTENTIC — Tahap 3: Admin Panel & Hak Akses

## Isi patch
- Login internal terpisah dari login customer (`/admin/login`).
- Setup Owner pertama kali (`/admin/setup-owner`), hanya tersedia sebelum ada Owner.
- Role bawaan: Owner / Super Admin, Admin, Staff, Courier.
- Permission berbasis role.
- Manajemen akun internal: tambah, ubah, aktif/nonaktif, ubah password, hapus.
- Manajemen role khusus (role sistem tidak dapat dihapus; Owner tidak dapat diubah).
- Dashboard internal.

## Cara memasang
1. Backup folder project.
2. Extract ZIP dan merge folder `app`, `bootstrap`, `database`, `resources`, `routes` ke root project.
3. Jangan timpa `.env`.
4. Jalankan:

```bash
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan route:list --path=admin
```

Migration baru hanya menambahkan tabel `roles`, `permissions`, pivot `permission_role`, serta kolom `role_id` dan `is_active` pada `users`. Tidak menghapus tabel atau data lama.

## Membuat Owner pertama
Buka `http://localhost/shoestentic/public/admin/setup-owner`, lalu buat akun Owner dengan password minimal 12 karakter. Setelah Owner berhasil dibuat, endpoint setup otomatis ditutup (404). Login internal melalui `/admin/login`.

Jika menggunakan `php artisan serve`, akses `http://127.0.0.1:8000/admin/setup-owner`.

## Catatan
- Login customer tetap menggunakan guard `customer` dan OTP WhatsApp.
- Login internal menggunakan guard `web` dan tabel `users`.
- Jangan jalankan `migrate:fresh`, `db:wipe`, atau menghapus database.
- Untuk production, hapus/disable akses setup Owner melalui deployment policy setelah akun Owner dibuat; route sendiri sudah terkunci setelah Owner pertama tersimpan.
