# SHOESTENTIC – Hotfix Route Kurir

Memperbaiki error:
Route [admin.shipments.index] not defined.

## Pemasangan
1. Backup `routes/web.php`.
2. Ekstrak ZIP.
3. Salin `routes/web.php` dari patch ke project `C:\xampp\htdocs\shoestentic\routes\web.php`.
4. Jalankan:

```bash
php artisan optimize:clear
php artisan route:list --path=admin/shipments
php artisan route:list --path=admin/kurir
```

Patch menambahkan route admin shipment, pembuatan pickup/delivery, penugasan kurir, update status, dan dashboard kurir. Tidak mengubah database, migration, `.env`, atau file lain.

Jika muncul 403 setelah route ditemukan, jalankan ulang RolePermissionSeeder yang disertakan di patch kurir sebelumnya:
`php artisan db:seed --class=RolePermissionSeeder`
