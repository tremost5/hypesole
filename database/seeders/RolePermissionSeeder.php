<?php
namespace Database\Seeders;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view'=>['Lihat Dashboard','Dashboard'],
            'users.view'=>['Lihat Akun Internal','Manajemen Akun'],
            'users.create'=>['Tambah Akun Internal','Manajemen Akun'],
            'users.update'=>['Ubah Akun Internal','Manajemen Akun'],
            'users.delete'=>['Hapus Akun Internal','Manajemen Akun'],
            'roles.view'=>['Lihat Role & Permission','Hak Akses'],
            'roles.create'=>['Tambah Role','Hak Akses'],
            'roles.update'=>['Ubah Role & Permission','Hak Akses'],
            'roles.delete'=>['Hapus Role','Hak Akses'],
            'catalog.view'=>['Lihat Master Data','Master Data'],
            'catalog.manage'=>['Kelola Kategori Layanan dan Produk','Master Data'],
            'services.view'=>['Lihat Layanan Shoe Care','Shoe Care'],
            'services.manage'=>['Kelola Layanan Shoe Care','Shoe Care'],
            'products.view'=>['Lihat Produk Sneaker Store','Sneaker Store'],
            'products.manage'=>['Kelola Produk Sneaker Store','Sneaker Store'],
            'inventory.view'=>['Lihat Riwayat Stok','Inventaris'],
            'inventory.manage'=>['Kelola Stok Produk','Inventaris'],
            'orders.view'=>['Lihat Order Shoe Care','Transaksi Shoe Care'],
            'orders.create'=>['Buat Order Shoe Care','Transaksi Shoe Care'],
            'orders.update'=>['Ubah Status Order Shoe Care','Transaksi Shoe Care'],
            'store-orders.view'=>['Lihat Pesanan Sneaker Store','Pesanan Sneaker Store'],
            'store-orders.manage'=>['Kelola Pesanan Sneaker Store','Pesanan Sneaker Store'],
            'shipments.view'=>['Lihat Pengiriman Shoe Care','Kurir & Pengiriman'],
            'shipments.manage'=>['Kelola Pickup, Delivery dan Kurir','Kurir & Pengiriman'],
            'courier.dashboard'=>['Akses Dashboard Kurir','Kurir & Pengiriman'],
            'website.manage'=>['Kelola konten website dan banner','Website & CMS'],
            'fonnte.manage'=>['Kelola konfigurasi WhatsApp Fonnte','Pengaturan Sistem'],
            'cashier.sales'=>['Akses transaksi kasir','Kasir'],
            'website.manage'=>['Kelola konten website dan banner','Website & CMS'],
            'fonnte.manage'=>['Kelola konfigurasi WhatsApp Fonnte','Pengaturan Sistem'],
            'cashier.sales'=>['Akses transaksi kasir','Kasir'],
        ];
        $models=[];
        foreach ($permissions as $slug=>[$name,$group]) {
            $models[$slug]=Permission::updateOrCreate(['slug'=>$slug],['name'=>$name,'group_name'=>$group]);
        }
        $definitions=[
            'owner'=>['Superadmin','Akses penuh seluruh sistem termasuk konfigurasi sensitif',array_keys($permissions),true],
            'admin'=>['Admin','Pengelolaan operasional, master data dan konten',array_values(array_diff(array_keys($permissions), ['users.delete','roles.view','roles.create','roles.update','roles.delete','fonnte.manage'])),true],
            'cashier'=>['Kasir','Akses transaksi dan pembayaran', ['dashboard.view','orders.view','orders.create','store-orders.view','cashier.sales'],true],
            'staff'=>['Staff','Penerimaan dan pengerjaan order Shoe Care',['dashboard.view','orders.view','orders.create','orders.update'],true],
            'courier'=>['Courier','Akses dashboard kurir',['dashboard.view','courier.dashboard'],true],
        ];
        foreach ($definitions as $slug=>[$name,$description,$grants,$system]) {
            $role=Role::updateOrCreate(['slug'=>$slug],['name'=>$name,'description'=>$description,'is_system'=>$system]);
            $role->permissions()->sync(collect($grants)->map(fn($key)=>$models[$key]->id)->all());
        }
    }
}
