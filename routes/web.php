<?php

use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\CustomerCartController;
use App\Http\Controllers\CustomerStoreOrderController;
use App\Http\Controllers\AdminStoreOrderController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminCatalogController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminBannerController;
use App\Http\Controllers\AdminFonnteController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminShoeCareOrderController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\AdminShipmentController;
use App\Http\Controllers\CourierController;
use App\Http\Middleware\EnsurePermission;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\ProtectedMediaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public PWA files
|--------------------------------------------------------------------------
|
| Some RumahWeb public directories deny direct access to uploaded static
| files. The matching .htaccess rewrites only these URLs to Laravel so they
| remain public while preserving their correct MIME types and worker scope.
*/
Route::get('/manifest.webmanifest', function () {
    return response()->file(public_path('manifest.webmanifest'), [
        'Content-Type' => 'application/manifest+json; charset=utf-8',
        'Cache-Control' => 'no-cache, must-revalidate',
    ]);
})->name('pwa.manifest');

Route::get('/service-worker.js', function (\Illuminate\Http\Request $request) {
    $appPath = parse_url((string) config('app.url'), PHP_URL_PATH);
    $basePath = is_string($appPath) && $appPath !== '' ? $appPath : $request->getBaseUrl();
    $scope = '/'.trim((string) $basePath, '/').'/';

    return response()->file(public_path('service-worker.js'), [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Cache-Control' => 'no-cache, must-revalidate',
        'Service-Worker-Allowed' => $scope,
    ]);
})->name('pwa.service-worker');

Route::get('/offline.html', function () {
    return response()->file(public_path('offline.html'), [
        'Content-Type' => 'text/html; charset=utf-8',
        'Cache-Control' => 'no-cache, must-revalidate',
    ]);
})->name('pwa.offline');

Route::get('/icons/{icon}', function (string $icon) {
    abort_unless(in_array($icon, ['icon-192.png', 'icon-512.png', 'icon-512-maskable.png'], true), 404);

    return response()->file(public_path('icons/'.$icon), [
        'Content-Type' => 'image/png',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('icon', 'icon-(192|512|512-maskable)\.png')->name('pwa.icon');

/*
|--------------------------------------------------------------------------
| PUBLIC SNEAKER STORE
|--------------------------------------------------------------------------
*/

Route::get('/', [StorefrontController::class, 'index'])
    ->name('home');

Route::get('/produk/{product:slug}', [StorefrontController::class, 'show'])
    ->name('storefront.products.show');

// Public guest booking via WhatsApp (does not require customer login).
Route::get('/booking/whatsapp', [\App\Http\Controllers\GuestWhatsAppBookingController::class, 'create'])
    ->name('guest.booking.whatsapp.create');
Route::post('/booking/whatsapp', [\App\Http\Controllers\GuestWhatsAppBookingController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('guest.booking.whatsapp.store');
Route::get('/booking/whatsapp/photo/{filename}', [\App\Http\Controllers\GuestWhatsAppBookingController::class, 'photo'])
    ->middleware('signed:relative')
    ->name('guest.booking.whatsapp.photo');


/*
|--------------------------------------------------------------------------
| CUSTOMER
|--------------------------------------------------------------------------
*/

Route::prefix('customer')
    ->name('customer.')
    ->group(function () {

        // Guest Customer
        Route::middleware('guest:customer')->group(function () {
            Route::get('/login', [CustomerAuthController::class, 'showLogin'])
                ->name('login');

            Route::post('/otp', [CustomerAuthController::class, 'requestOtp'])
                ->middleware('throttle:10,60')
                ->name('otp.request');

            Route::get('/otp', [CustomerAuthController::class, 'showOtpForm'])
                ->name('otp.form');

            Route::post('/otp/verify', [CustomerAuthController::class, 'verifyOtp'])
                ->middleware('throttle:10,15')
                ->name('otp.verify');
        });

        // Authenticated Customer
        Route::middleware('auth:customer')->group(function () {
            Route::get('/booking', [CustomerBookingController::class, 'create'])->name('booking.create');
            Route::post('/booking', [CustomerBookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store');

            Route::get('/dashboard', [CustomerAuthController::class, 'dashboard'])
                ->name('dashboard');

            Route::post('/logout', [CustomerAuthController::class, 'logout'])
                ->name('logout');

            // Profile
            Route::put('/profile', [CustomerAccountController::class, 'updateProfile'])
                ->name('profile.update');

            // Addresses
            Route::post('/addresses', [CustomerAccountController::class, 'storeAddress'])
                ->name('addresses.store');

            Route::put('/addresses/{address}', [CustomerAccountController::class, 'updateAddress'])
                ->name('addresses.update');

            Route::patch('/addresses/{address}/default', [CustomerAccountController::class, 'setDefaultAddress'])
                ->name('addresses.default');

            Route::delete('/addresses/{address}', [CustomerAccountController::class, 'destroyAddress'])
                ->name('addresses.destroy');

            // Shoe Care Orders
            Route::get('/orders', [CustomerOrderController::class, 'index'])
                ->name('orders.index');

            Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])
                ->name('orders.show');

            Route::post('/orders/{order}/items/{item}/approval', [CustomerOrderController::class, 'approveItem'])
                ->name('orders.items.approval');

            // Sneaker Cart
            Route::get('/keranjang', [CustomerCartController::class, 'index'])
                ->name('cart.index');

            Route::post('/keranjang/{product}', [CustomerCartController::class, 'add'])
                ->name('cart.add');

            Route::put('/keranjang/{product}', [CustomerCartController::class, 'update'])
                ->name('cart.update');

            Route::delete('/keranjang/{product}', [CustomerCartController::class, 'remove'])
                ->name('cart.remove');

            // Sneaker Checkout
            Route::get('/checkout', [CustomerStoreOrderController::class, 'checkout'])
                ->name('checkout');

            Route::post('/checkout', [CustomerStoreOrderController::class, 'place'])
                ->name('checkout.place');

            // Sneaker Store Orders
            Route::get('/pesanan-toko', [CustomerStoreOrderController::class, 'index'])
                ->name('store-orders.index');

            Route::get('/pesanan-toko/{order}', [CustomerStoreOrderController::class, 'show'])
                ->name('store-orders.show');
        });
    });

Route::middleware('auth:customer')->get('/customer/media/orders/{order}/{item}/{kind}/{index}', [ProtectedMediaController::class, 'orderPhoto'])->name('customer.media.order-photo');
Route::middleware('auth:customer')->get('/customer/media/shipments/{shipment}', [ProtectedMediaController::class, 'shipmentProof'])->name('customer.media.shipment-proof');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Guest Admin
        Route::middleware('guest:web')->group(function () {

            Route::get('/login', [AdminAuthController::class, 'showLogin'])
                ->name('login');

            Route::post('/login', [AdminAuthController::class, 'login'])
                ->middleware('throttle:5,1')
                ->name('login.submit');

        });

        // Authenticated Admin
        Route::middleware('auth:web')->group(function () {
            Route::get('/media/orders/{order}/{item}/{kind}/{index}', [ProtectedMediaController::class, 'orderPhoto'])->name('media.order-photo');
            Route::get('/media/shipments/{shipment}', [ProtectedMediaController::class, 'shipmentProof'])->name('media.shipment-proof');

            Route::post('/logout', [AdminAuthController::class, 'logout'])
                ->name('logout');

            // Dashboard
            Route::get('/', [AdminDashboardController::class, 'index'])
                ->middleware(EnsurePermission::class . ':dashboard.view')
                ->name('dashboard');

            // Users
            Route::get('/users', [AdminUserController::class, 'index'])
                ->middleware(EnsurePermission::class . ':users.view')
                ->name('users.index');

            Route::post('/users', [AdminUserController::class, 'store'])
                ->middleware(EnsurePermission::class . ':users.create')
                ->name('users.store');

            Route::put('/users/{user}', [AdminUserController::class, 'update'])
                ->middleware(EnsurePermission::class . ':users.update')
                ->name('users.update');

            Route::put('/users/{user}/password', [AdminUserController::class, 'resetPassword'])
                ->middleware(EnsurePermission::class . ':users.update')
                ->name('users.password');

            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
                ->middleware(EnsurePermission::class . ':users.delete')
                ->name('users.destroy');

            // Roles
            Route::get('/roles', [AdminRoleController::class, 'index'])
                ->middleware(EnsurePermission::class . ':roles.view')
                ->name('roles.index');

            Route::post('/roles', [AdminRoleController::class, 'store'])
                ->middleware(EnsurePermission::class . ':roles.create')
                ->name('roles.store');

            Route::put('/roles/{role}', [AdminRoleController::class, 'update'])
                ->middleware(EnsurePermission::class . ':roles.update')
                ->name('roles.update');

            Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])
                ->middleware(EnsurePermission::class . ':roles.delete')
                ->name('roles.destroy');

            // Catalog
            Route::get('/catalog', [AdminCatalogController::class, 'index'])
                ->middleware(EnsurePermission::class . ':catalog.view')
                ->name('catalog.index');

            Route::post('/catalog/service-categories', [AdminCatalogController::class, 'storeServiceCategory'])
                ->middleware(EnsurePermission::class . ':catalog.manage')
                ->name('catalog.service-categories.store');

            Route::put('/catalog/service-categories/{category}', [AdminCatalogController::class, 'updateServiceCategory'])
                ->middleware(EnsurePermission::class . ':catalog.manage')
                ->name('catalog.service-categories.update');

            Route::delete('/catalog/service-categories/{category}', [AdminCatalogController::class, 'destroyServiceCategory'])
                ->middleware(EnsurePermission::class . ':catalog.manage')
                ->name('catalog.service-categories.destroy');

            Route::post('/catalog/product-categories', [AdminCatalogController::class, 'storeProductCategory'])
                ->middleware(EnsurePermission::class . ':catalog.manage')
                ->name('catalog.product-categories.store');

            Route::put('/catalog/product-categories/{category}', [AdminCatalogController::class, 'updateProductCategory'])
                ->middleware(EnsurePermission::class . ':catalog.manage')
                ->name('catalog.product-categories.update');

            Route::delete('/catalog/product-categories/{category}', [AdminCatalogController::class, 'destroyProductCategory'])
                ->middleware(EnsurePermission::class . ':catalog.manage')
                ->name('catalog.product-categories.destroy');

            // Services
            Route::get('/services', [AdminServiceController::class, 'index'])
                ->middleware(EnsurePermission::class . ':services.view')
                ->name('services.index');

            Route::post('/services', [AdminServiceController::class, 'store'])
                ->middleware(EnsurePermission::class . ':services.manage')
                ->name('services.store');

            Route::put('/services/{service}', [AdminServiceController::class, 'update'])
                ->middleware(EnsurePermission::class . ':services.manage')
                ->name('services.update');

            Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])
                ->middleware(EnsurePermission::class . ':services.manage')
                ->name('services.destroy');

            // Products
            Route::get('/products', [AdminProductController::class, 'index'])
                ->middleware(EnsurePermission::class . ':products.view')
                ->name('products.index');

            Route::post('/products', [AdminProductController::class, 'store'])
                ->middleware(EnsurePermission::class . ':products.manage')
                ->name('products.store');

            Route::put('/products/{product}', [AdminProductController::class, 'update'])
                ->middleware(EnsurePermission::class . ':products.manage')
                ->name('products.update');

            Route::patch('/products/{product}/stock', [AdminProductController::class, 'adjustStock'])
                ->middleware(EnsurePermission::class . ':inventory.manage')
                ->name('products.stock');

            Route::get('/products/{product}/movements', [AdminProductController::class, 'movements'])
                ->middleware(EnsurePermission::class . ':inventory.view')
                ->name('products.movements');

            Route::patch('/products/{product}/archive', [AdminProductController::class, 'archive'])
                ->middleware(EnsurePermission::class . ':products.manage')
                ->name('products.archive');

            // Shoe Care Orders
            Route::get('/shoe-care-orders', [AdminShoeCareOrderController::class, 'index'])
                ->middleware(EnsurePermission::class . ':orders.view')
                ->name('shoe-care-orders.index');

            Route::get('/shoe-care-orders/create', [AdminShoeCareOrderController::class, 'create'])
                ->middleware(EnsurePermission::class . ':orders.create')
                ->name('shoe-care-orders.create');

            Route::post('/shoe-care-orders', [AdminShoeCareOrderController::class, 'store'])
                ->middleware(EnsurePermission::class . ':orders.create')
                ->name('shoe-care-orders.store');

            Route::get('/shoe-care-orders/{order}', [AdminShoeCareOrderController::class, 'show'])
                ->middleware(EnsurePermission::class . ':orders.view')
                ->name('shoe-care-orders.show');

            Route::patch('/shoe-care-orders/{order}/status', [AdminShoeCareOrderController::class, 'updateStatus'])
                ->middleware(EnsurePermission::class . ':orders.update')
                ->name('shoe-care-orders.status');

            Route::patch('/shoe-care-orders/{order}/items/{item}/status', [AdminShoeCareOrderController::class, 'updateItemStatus'])
                ->middleware(EnsurePermission::class . ':orders.update')
                ->name('shoe-care-orders.items.status');
            Route::patch('/shoe-care-orders/{order}/items/{item}/quote', [AdminShoeCareOrderController::class, 'quoteItem'])
                ->middleware(EnsurePermission::class . ':orders.update')
                ->name('shoe-care-orders.items.quote');

            // Courier & Shipments
            Route::get('/shipments', [AdminShipmentController::class, 'index'])
                ->middleware(EnsurePermission::class . ':shipments.view')
                ->name('shipments.index');

            Route::get('/shoe-care-orders/{order}/shipments/create', [AdminShipmentController::class, 'create'])
                ->middleware(EnsurePermission::class . ':shipments.manage')
                ->name('shipments.create');

            Route::post('/shoe-care-orders/{order}/shipments', [AdminShipmentController::class, 'store'])
                ->middleware(EnsurePermission::class . ':shipments.manage')
                ->name('shipments.store');

            Route::patch('/shipments/{shipment}/assign', [AdminShipmentController::class, 'assign'])
                ->middleware(EnsurePermission::class . ':shipments.manage')
                ->name('shipments.assign');

            Route::patch('/shipments/{shipment}/status', [AdminShipmentController::class, 'updateStatus'])
                ->middleware(EnsurePermission::class . ':shipments.manage')
                ->name('shipments.status');

            // Courier Dashboard
            Route::get('/kurir', [CourierController::class, 'index'])
                ->middleware(EnsurePermission::class . ':courier.dashboard')
                ->name('courier.dashboard');

            Route::patch('/kurir/shipments/{shipment}/status', [CourierController::class, 'updateStatus'])
                ->middleware(EnsurePermission::class . ':courier.dashboard')
                ->name('courier.shipments.status');

            // Website CMS and WhatsApp gateway (Superadmin/Admin permissions)
            Route::get('/banners', [AdminBannerController::class, 'index'])->middleware(EnsurePermission::class . ':website.manage')->name('banners.index');
            Route::post('/banners', [AdminBannerController::class, 'store'])->middleware(EnsurePermission::class . ':website.manage')->name('banners.store');
            Route::put('/banners/{banner}', [AdminBannerController::class, 'update'])->middleware(EnsurePermission::class . ':website.manage')->name('banners.update');
            Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->middleware(EnsurePermission::class . ':website.manage')->name('banners.destroy');
            Route::get('/settings/fonnte', [AdminFonnteController::class, 'edit'])->middleware(EnsurePermission::class . ':fonnte.manage')->name('fonnte.edit');
            Route::put('/settings/fonnte', [AdminFonnteController::class, 'update'])->middleware(EnsurePermission::class . ':fonnte.manage')->name('fonnte.update');
            Route::post('/settings/fonnte/test', [AdminFonnteController::class, 'test'])->middleware(EnsurePermission::class . ':fonnte.manage')->name('fonnte.test');

            // Sneaker Store Orders
            Route::get('/store-orders', [AdminStoreOrderController::class, 'index'])
                ->middleware(EnsurePermission::class . ':store-orders.view')
                ->name('store-orders.index');

            Route::get('/store-orders/{order}', [AdminStoreOrderController::class, 'show'])
                ->middleware(EnsurePermission::class . ':store-orders.view')
                ->name('store-orders.show');

            Route::patch('/store-orders/{order}', [AdminStoreOrderController::class, 'update'])
                ->middleware(EnsurePermission::class . ':store-orders.manage')
                ->name('store-orders.update');
        });
    });

