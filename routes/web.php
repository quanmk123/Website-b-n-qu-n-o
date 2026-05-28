<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BaiVietController;
use App\Http\Controllers\Admin\DanhMucController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\BaiVietController as UserBaiVietController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\DonHangController;
use App\Http\Controllers\user\ShopController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'product_detail'])->name('product.detail');
Route::get('/search-suggestions', [ShopController::class, 'searchSuggestions'])->name('search.suggestions');

Route::get('/posts', [UserBaiVietController::class, 'index'])->name('post.index');
Route::get('/post/{slug}', [UserBaiVietController::class, 'post_detail'])->name('post.detail');

// Static pages
Route::get('/about', [App\Http\Controllers\HomeController::class, 'about'])->name('about.index');
Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact'])->name('contact.index');
Route::post('/contact', [App\Http\Controllers\HomeController::class, 'postContact'])->name('client.postContact');

// cart
Route::get('/list-cart', [CartController::class, 'listCart'])->name('cart.list');
Route::post('/add-to-cart', [CartController::class, 'addCart'])->name('cart.add');
Route::post('/update-cart', [CartController::class, 'updateCart'])->name('cart.update');
Route::post('/remove-cart', [CartController::class, 'removeCart'])->name('cart.remove');

Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::resource('/address', AddressController::class);



    // order
    Route::get('/don-hang', [DonHangController::class, 'index'])->name('donhangs.index');
    Route::match(['get', 'post'], '/don-hang/create', [DonHangController::class, 'create'])->name('donhangs.create');
    Route::post('/don-hang/store', [DonHangController::class, 'store'])->name('donhangs.store');
    Route::get('/don-hang/show/{id}', [DonHangController::class, 'show'])->name('donhangs.show');
    Route::get('/don-hang/update/{id}', [DonHangController::class, 'update'])->name('donhangs.update');
    
    // User Profile
    Route::get('/account-details', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/account-details', [UserController::class, 'updateProfile'])->name('user.profile.update');

    // Wishlist
    Route::get('/wishlist', [\App\Http\Controllers\User\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [\App\Http\Controllers\User\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{id}', [\App\Http\Controllers\User\WishlistController::class, 'remove'])->name('wishlist.remove');
    
    // Coupon actions
    Route::post('/don-hang/coupon/apply', [DonHangController::class, 'applyCoupon'])->name('donhangs.coupon.apply');
    Route::get('/don-hang/coupon/remove', [DonHangController::class, 'removeCoupon'])->name('donhangs.coupon.remove');
    
    // Re-order
    Route::post('/don-hang/reorder/{id}', [DonHangController::class, 'reorder'])->name('donhangs.reorder');

});

Route::middleware(['auth', AuthAdmin::class])->group(function () {
    Route::get('/admin/index', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::put('/admin/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::resource('admin/danh_mucs', DanhMucController::class);
    Route::resource('admin/san_phams', SanPhamController::class);
    Route::resource('admin/bai_viets', BaiVietController::class);
    
    // Admin Order Routes
    Route::get('/admin/orders', [\App\Http\Controllers\Admin\DonHangController::class, 'index'])->name('admin.orders.index');
    Route::get('/admin/orders/track', [\App\Http\Controllers\Admin\DonHangController::class, 'track'])->name('admin.orders.track');
    Route::get('/admin/orders/{id}', [\App\Http\Controllers\Admin\DonHangController::class, 'show'])->name('admin.orders.show');
    Route::put('/admin/orders/{id}', [\App\Http\Controllers\Admin\DonHangController::class, 'update'])->name('admin.orders.update');

    // Admin Coupon Routes
    Route::resource('admin/coupons', \App\Http\Controllers\Admin\CouponController::class)->names('admin.coupons');

    // Admin Slider Routes
    Route::resource('admin/sliders', \App\Http\Controllers\Admin\SliderController::class)->names('admin.sliders');

    // Admin User Routes
    Route::resource('admin/users', \App\Http\Controllers\Admin\UserController::class)->names('admin.users');

    // Admin Contact Routes
    Route::get('admin/lien-he', [\App\Http\Controllers\Client\LienHeController::class, 'admin_contact'])->name('admin.lien_hes.index');
});
