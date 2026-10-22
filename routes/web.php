<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BundleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\JournalPostController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\OperationsController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReturnRequestController as AdminReturnRequestController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ServiceabilityController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\GoogleAuthController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Frontend\ReturnRequestController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'create'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->middleware('throttle:registration');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:login');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->middleware('throttle:social-login')->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->middleware('throttle:social-login')->name('auth.google.callback');
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:password-reset')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])->middleware('throttle:password-update')->name('password.update');
    Route::get('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'authenticateAdmin'])->middleware('throttle:admin-login')->name('admin.login.store');
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware(['auth', 'throttle:logout'])->name('logout');
Route::get('/refer/{code}', [AuthController::class, 'refer'])->middleware('throttle:referrals')->name('refer');
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed', 'throttle:verification'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'sendVerification'])->middleware('throttle:verification')->name('verification.send');
});

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->middleware('throttle:contact')->name('contact.send');
Route::get('/blog', [PageController::class, 'blog']);
Route::get('/blog/{slug}', fn (string $slug) => redirect()->route('blog.show', $slug, 301));
Route::get('/journal', [PageController::class, 'blog'])->name('blog');
Route::get('/journal/feed.xml', [PageController::class, 'feed'])->name('blog.feed');
Route::get('/journal/{slug}', [PageController::class, 'post'])->name('blog.show');
Route::view('/faq', 'frontend.pages.faq')->name('faq');
Route::view('/shipping-policy', 'frontend.pages.policy', ['title' => 'Shipping policy', 'kind' => 'shipping'])->name('shipping.policy');
Route::view('/return-policy', 'frontend.pages.policy', ['title' => 'Returns & refunds', 'kind' => 'returns'])->name('returns.policy');
Route::view('/privacy-policy', 'frontend.pages.policy', ['title' => 'Privacy policy', 'kind' => 'privacy'])->name('privacy.policy');
Route::view('/terms', 'frontend.pages.policy', ['title' => 'Terms & conditions', 'kind' => 'terms'])->name('terms');
Route::view('/editorial-policy', 'frontend.pages.editorial-policy')->name('editorial.policy');
Route::view('/rewards-and-referrals', 'frontend.pages.rewards')->name('rewards');
Route::get('/sitemap.xml', [PageController::class, 'sitemapIndex'])->name('sitemap');
Route::get('/sitemaps/pages.xml', [PageController::class, 'pagesSitemap'])->name('sitemaps.pages');
Route::get('/sitemaps/categories.xml', [PageController::class, 'categoriesSitemap'])->name('sitemaps.categories');
Route::get('/sitemaps/journal.xml', [PageController::class, 'journalSitemap'])->name('sitemaps.journal');
Route::get('/sitemaps/products-{category}-{page}.xml', [PageController::class, 'productsSitemap'])
    ->where(['category' => '[a-z0-9-]+', 'page' => '[1-9][0-9]*'])
    ->name('sitemaps.products');
Route::get('/robots.txt', [PageController::class, 'robots'])->name('robots');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('two-factor', [TwoFactorController::class, 'verify'])->middleware('throttle:admin-2fa')->name('two-factor.verify');
    Route::post('two-factor/resend', [TwoFactorController::class, 'resend'])->middleware('throttle:admin-2fa-resend')->name('two-factor.resend');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'admin.2fa', 'admin.permission', 'admin.audit', 'throttle:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::patch('/maintenance', [MaintenanceController::class, 'update'])->middleware('throttle:admin-sensitive')->name('maintenance.update');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::patch('/notifications/{notification}', [NotificationController::class, 'update'])->name('notifications.update');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');
    Route::resource('coupons', CouponController::class)->except('show');
    Route::resource('bundles', BundleController::class)->except('show');
    Route::resource('journals', JournalPostController::class)->except('show');
    Route::resource('hero-slides', HeroSlideController::class)->except('show');
    Route::get('customers', [OperationsController::class, 'customers'])->name('customers.index');
    Route::get('customers-export', [OperationsController::class, 'exportCustomers'])->name('customers.export');
    Route::get('customers/{user}', [OperationsController::class, 'customer'])->name('customers.show');
    Route::post('customers/{user}/points', [OperationsController::class, 'adjustPoints'])->middleware('throttle:admin-sensitive')->name('customers.points');
    Route::get('reports', [OperationsController::class, 'reports'])->name('reports.index');
    Route::get('reports-export', [OperationsController::class, 'exportReports'])->name('reports.export');
    Route::get('orders/{order}/invoice', [OperationsController::class, 'invoice'])->name('orders.invoice');
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('inventory', [ProductController::class, 'inventory'])->name('inventory.index');
    Route::patch('inventory/{product}', [ProductController::class, 'updateStock'])->name('inventory.update');
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
    Route::post('orders/{order}/refund', [OrderController::class, 'refund'])->middleware('throttle:admin-sensitive')->name('orders.refund');
    Route::resource('serviceability', ServiceabilityController::class)->except('show');
    Route::post('serviceability-import', [ServiceabilityController::class, 'import'])->middleware('throttle:admin-sensitive')->name('serviceability.import');
    Route::get('serviceability-export', [ServiceabilityController::class, 'export'])->name('serviceability.export');
    Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::patch('contact-messages/{contactMessage}', [ContactMessageController::class, 'update'])->name('contact-messages.update');
    Route::delete('contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
    Route::get('team', [TeamController::class, 'index'])->name('team.index');
    Route::post('team', [TeamController::class, 'store'])->middleware('throttle:admin-sensitive')->name('team.store');
    Route::patch('team/{user}', [TeamController::class, 'update'])->middleware('throttle:admin-sensitive')->name('team.update');
    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
    Route::get('returns', [AdminReturnRequestController::class, 'index'])->name('returns.index');
    Route::patch('returns/{returnRequest}', [AdminReturnRequestController::class, 'update'])->name('returns.update');
});

Route::get('/search/suggestions', [FrontendProductController::class, 'suggestions'])->middleware('throttle:search')->name('products.suggestions');
Route::get('/products', [FrontendProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [FrontendProductController::class, 'show'])->name('products.show');
Route::get('/bundles', [FrontendProductController::class, 'bundles'])->name('bundles.index');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->middleware('throttle:cart-actions')->name('cart.add');
Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->middleware('throttle:cart-actions')->name('cart.update');
Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->middleware('throttle:cart-actions')->name('cart.remove');
Route::post('/cart/bundles/{bundle}', [CartController::class, 'addBundle'])->middleware('throttle:cart-actions')->name('cart.bundles.add');
Route::patch('/cart/bundles/{cartBundleItem}', [CartController::class, 'updateBundle'])->middleware('throttle:cart-actions')->name('cart.bundles.update');
Route::delete('/cart/bundles/{cartBundleItem}', [CartController::class, 'removeBundle'])->middleware('throttle:cart-actions')->name('cart.bundles.remove');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->middleware('throttle:commerce-actions')->name('cart.coupon.apply');
Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->middleware('throttle:commerce-actions')->name('cart.coupon.remove');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->middleware('throttle:notifications')->name('notifications.read-all');
    Route::patch('/notifications/{notification}', [NotificationController::class, 'update'])->middleware('throttle:notifications')->name('notifications.update');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->middleware('throttle:notifications')->name('notifications.destroy');
    Route::get('/checkout', [CheckoutController::class, 'index'])->middleware('verified')->name('checkout.index');
    Route::post('/checkout/quote', [CheckoutController::class, 'quote'])->middleware(['verified', 'throttle:checkout-quote'])->name('checkout.quote');
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->middleware(['verified', 'throttle:checkout'])->name('checkout.placeOrder');
    Route::get('/order-success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/stripe/checkout/{order}', [CheckoutController::class, 'stripeCheckout'])->middleware('throttle:payment')->name('stripe.checkout');
    Route::get('/account', [AccountController::class, 'show'])->name('profile');
    Route::put('/account', [AccountController::class, 'update'])->middleware('throttle:profile')->name('profile.update');
    Route::get('/my-orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/my-orders/{order}/track', [AccountController::class, 'track'])->name('orders.track');
    Route::get('/my-orders/{order}', [AccountController::class, 'order'])->name('orders.show');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'store'])->middleware('throttle:wishlist')->name('wishlist.store');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->middleware('throttle:wishlist')->name('wishlist.destroy');
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->middleware('throttle:reviews')->name('reviews.store');
    Route::post('/my-orders/{order}/return', [ReturnRequestController::class, 'store'])->middleware('throttle:returns')->name('returns.store');
});
Route::post('/stripe/webhook', [CheckoutController::class, 'stripeWebhook'])->middleware('throttle:stripe-webhooks')->name('stripe.webhook');
Route::post('/pincode-check', [FrontendProductController::class, 'pincode'])->middleware('throttle:pincode')->name('pincode.check');
