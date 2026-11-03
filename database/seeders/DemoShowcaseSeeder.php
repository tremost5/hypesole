<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ShoeCareOrder;
use App\Models\ShoeCareOrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Safe, repeatable showcase data for the HYPESOLE landing page.
 * Uses reserved demo SKUs, phone numbers and order numbers; never deletes user data.
 */
class DemoShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $category = ProductCategory::updateOrCreate(
                ['slug' => 'sneakers-demo'],
                ['name' => 'Sneakers', 'description' => 'Kategori contoh untuk preview Sneaker Store HYPESOLE.', 'is_active' => true]
            );

            $products = [
                ['HY-DEMO-NEW-001','Volt Runner X','AERON','new','42','Sneaker baru, belum pernah dipakai.','Sneaker sporty dengan siluet modern untuk aktivitas harian.','1299000',8,'new-01.svg'],
                ['HY-DEMO-NEW-002','Cloud Street Pro','NORTHLINE','new','41','Kondisi baru, box dan kelengkapan tersedia.','Kenyamanan ringan dengan tampilan clean minimalis.','1149000',6,'new-02.svg'],
                ['HY-DEMO-NEW-003','Court Classic 82','COURT LAB','new','42','Kondisi baru dan belum digunakan.','Gaya court klasik yang mudah dipadukan dengan outfit kasual.','899000',5,'new-03.svg'],
                ['HY-DEMO-NEW-004','Urban Flex Low','URBANFORM','new','43','Kondisi baru, original packaging.','Sneaker low-cut dengan karakter streetwear yang versatile.','1050000',4,'new-04.svg'],
                ['HY-DEMO-NEW-005','Aero Knit One','PACEWORKS','new','40','Kondisi baru, upper knit breathable.','Upper rajut ringan untuk pemakaian aktif sehari-hari.','975000',7,'new-05.svg'],
                ['HY-DEMO-USED-001','Retro Canvas 90s','VINTAGE DEPT','preloved','41','Kondisi 85%, terdapat pemakaian wajar pada outsole.','Sneaker canvas bergaya retro, sudah melalui pengecekan kondisi.','425000',1,'used-01.svg'],
                ['HY-DEMO-USED-002','Vintage Runner Grey','NORTHLINE','preloved','42','Kondisi 90%, upper rapi dan outsole masih tebal.','Runner preloved dengan warna netral dan nyaman dipakai.','550000',1,'used-02.svg'],
                ['HY-DEMO-USED-003','Classic Dunk Red','COURT LAB','preloved','39','Kondisi 80%, ada crease ringan pada toe box.','Model klasik dengan karakter warna berani.','675000',1,'used-03.svg'],
                ['HY-DEMO-USED-004','Neo Low Essential','URBANFORM','preloved','40','Kondisi 88%, sudah dibersihkan dan siap pakai.','Sneaker low-top preloved dengan tampilan clean.','390000',1,'used-04.svg'],
                ['HY-DEMO-USED-005','Heritage Court Gum','VINTAGE DEPT','preloved','42','Kondisi 82%, tanda pemakaian normal.','Court sneaker dengan gum sole dan nuansa vintage.','485000',1,'used-05.svg'],
            ];

            foreach ($products as [$sku,$name,$brand,$type,$size,$condition,$description,$price,$stock,$image]) {
                Product::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'product_category_id' => $category->id,
                        'name' => $name,
                        'slug' => strtolower($sku),
                        'brand' => $brand,
                        'product_type' => $type,
                        'size' => $size,
                        'condition_note' => $condition,
                        'description' => $description,
                        'price' => $price,
                        'cost_price' => null,
                        'stock' => $stock,
                        'image_path' => 'images/demo/products/'.$image,
                        'is_active' => true,
                    ]
                );
            }

            $customers = [
                ['Raka Pratama','628111000001','Clean white leather','Nike','Air Force Style','White','42','Deep Cleaning','images/demo/gallery/gallery-01.svg'],
                ['Nadia Putri','628111000002','Daily runner refresh','Adidas','Cloud Runner','Grey','39','Deep Cleaning','images/demo/gallery/gallery-02.svg'],
                ['Dimas Saputra','628111000003','Canvas shoe revival','Converse','Chuck Canvas','Black','41','Fast Cleaning','images/demo/gallery/gallery-03.svg'],
                ['Alya Maharani','628111000004','Suede care treatment','New Balance','Classic Suede','Beige','38','Suede Treatment','images/demo/gallery/gallery-04.svg'],
                ['Fajar Ramadhan','628111000005','Sole restoration','Puma','Street Court','White / Navy','42','Sole Care','images/demo/gallery/gallery-05.svg'],
            ];

            foreach ($customers as $index => [$name,$phone,$story,$brand,$model,$color,$size,$service,$image]) {
                $customer = Customer::updateOrCreate(
                    ['phone' => $phone],
                    ['name' => $name]
                );
                $orderNumber = sprintf('DEMO-HY-%04d', $index + 1);
                $date = Carbon::now()->subDays(12 - ($index * 2));
                $order = ShoeCareOrder::updateOrCreate(
                    ['order_number' => $orderNumber],
                    [
                        'customer_id' => $customer->id,
                        'service_id' => null,
                        'received_by_user_id' => null,
                        'assigned_staff_id' => null,
                        'customer_name' => $name,
                        'customer_phone' => $phone,
                        'service_name' => $service,
                        'service_price' => 65000 + ($index * 10000),
                        'shoe_brand' => $brand,
                        'shoe_model' => $model,
                        'shoe_color' => $color,
                        'shoe_size' => $size,
                        'issue_description' => $story,
                        'customer_notes' => 'Data contoh untuk preview galeri landing page HYPESOLE.',
                        'internal_notes' => 'DEMO DATA — bukan transaksi pelanggan asli.',
                        'status' => 'delivered',
                        'before_photos' => ['images/demo/gallery/'.$this->beforeImage($index)],
                        'after_photos' => [$image],
                        'received_at' => $date,
                        'estimated_completion_at' => $date->copy()->addDay(),
                        'completed_at' => $date->copy()->addDay(),
                        'delivered_at' => $date->copy()->addDay(),
                    ]
                );

                ShoeCareOrderItem::updateOrCreate(
                    ['shoe_care_order_id' => $order->id],
                    [
                        'service_id' => null,
                        'assigned_staff_id' => null,
                        'service_name' => $service,
                        'service_price' => 65000 + ($index * 10000),
                        'quoted_price' => 65000 + ($index * 10000),
                        'inspection_notes' => 'Pemeriksaan contoh untuk galeri demo.',
                        'customer_approval' => 'approved',
                        'customer_approved_at' => $date->copy()->addHours(2),
                        'shoe_brand' => $brand,
                        'shoe_model' => $model,
                        'shoe_color' => $color,
                        'shoe_size' => $size,
                        'issue_description' => $story,
                        'internal_notes' => 'DEMO DATA — bukan transaksi pelanggan asli.',
                        'status' => 'delivered',
                        'before_photos' => ['images/demo/gallery/'.$this->beforeImage($index)],
                        'after_photos' => [$image],
                    ]
                );
            }
        });
    }

    private function beforeImage(int $index): string
    {
        return 'gallery-'.str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT).'.svg';
    }
}
