<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::loginProcess');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::registerProcess');
$routes->get('/forgot-password', 'AuthController::forgotPassword');
$routes->post('/forgot-password', 'AuthController::forgotPasswordProcess');
$routes->get('/reset-password', 'AuthController::resetPassword');
$routes->post('/reset-password', 'AuthController::resetPasswordProcess');
/*
|--------------------------------------------------------------------------
| DASHBOARD (ROLE BASED)
|--------------------------------------------------------------------------
*/
$routes->get('/dashboard', 'DashboardController::index');

/*
|--------------------------------------------------------------------------
| EVENTS (PUBLIC AFTER LOGIN)
|--------------------------------------------------------------------------
*/
$routes->get('/events', 'EventController::index');
$routes->get('/events/(:num)', 'EventController::show/$1');

/*
|--------------------------------------------------------------------------
| PURCHASE / TICKETING
|--------------------------------------------------------------------------
*/
$routes->get('/buy/(:num)', 'PurchaseController::buy/$1');
$routes->get('/my-tickets', 'MyTicketController::index');
$routes->get('/tickets/view/(:num)', 'MyTicketController::view/$1');
// USER REFUND
$routes->post('/refund/request', 'RefundController::request');


/*
|--------------------------------------------------------------------------
| CHAT USER
|--------------------------------------------------------------------------
*/
$routes->get('/chat', 'ChatController::index');
$routes->post('/chat/send', 'ChatController::send');

/*
|--------------------------------------------------------------------------
| CHAT ADMIN / AGENT
|--------------------------------------------------------------------------
*/
$routes->get('/admin/chats', 'AdminChatController::index');
$routes->get('/admin/chat/(:num)', 'AdminChatController::show/$1');
$routes->post('/admin/chat/send', 'AdminChatController::send');

// ADMIN EVENTS
$routes->get('/admin/events', 'AdminEventController::index');
$routes->get('/admin/events/create', 'AdminEventController::create');
$routes->post('/admin/events/store', 'AdminEventController::store');
$routes->get('/admin/events/edit/(:num)', 'AdminEventController::edit/$1');
$routes->post('/admin/events/update/(:num)', 'AdminEventController::update/$1');
$routes->get('admin/events/delete/(:num)', 'AdminEventController::delete/$1');

$routes->get('/checkout/(:num)', 'CheckoutController::index/$1');
$routes->post('/checkout/process', 'CheckoutController::process');
$routes->get('/checkout/billing', 'CheckoutController::billing');
$routes->post('/checkout/submit-payment', 'CheckoutController::submitPayment');
$routes->get('checkout/calculate/(:num)', 'CheckoutController::calculate/$1'); // Route baru
// Tambahkan route ini di file routes
$routes->get('api/checkout/check-quota/(:num)', 'CheckoutController::checkQuota/$1');

$routes->get('/admin/reports', 'AdminReportController::index');
$routes->get('/admin/reports/load-more', 'AdminReportController::loadMoreTransactions');
$routes->get('/admin/reports/export-pdf', 'AdminReportController::exportPdf');

// ADMIN PAYMENTS
$routes->get('/admin/payments/pending', 'AdminPaymentController::pending');
$routes->post('/admin/payments/approve/(:num)', 'AdminPaymentController::approve/$1');
$routes->post('/admin/payments/reject/(:num)', 'AdminPaymentController::reject/$1');
$routes->get('/admin/payments/view-proof/(:num)', 'AdminPaymentController::viewProof/$1');
$routes->get('/admin/payments/download-proof/(:num)', 'AdminPaymentController::downloadProof/$1');
// Resend tickets for paid payments (admin)
$routes->get('/admin/payments/resend/(:num)', 'AdminPaymentController::resend/$1');

$routes->get('/admin/refunds', 'AdminRefundController::index');
$routes->get('/admin/refunds/(:num)', 'AdminRefundController::show/$1');
$routes->post('/admin/refunds/approve/(:num)', 'AdminRefundController::approve/$1');
$routes->post('/admin/refunds/reject/(:num)', 'AdminRefundController::reject/$1');

$routes->get('/test-email', 'TestEmailController::index');

$routes->get('/test-approval-email', 'TestApprovalEmail::index');
// Debug resend (no auth) - temporary
$routes->get('/debug/resend/(:num)', 'DebugResendController::resend/$1');

$routes->get('/events/favorites', 'EventController::favorites');
$routes->get('/events/favorite/(:num)', 'EventController::toggleFavorite/$1');

/*
|--------------------------------------------------------------------------
| QR CODE
|--------------------------------------------------------------------------
*/
$routes->get('/qr/generate/(:num)', 'QrController::generate/$1');
$routes->get('/admin/qr/verify', 'QrController::verify');
$routes->get('/admin/qr/verify/(:any)', 'QrController::verify/$1');
$routes->post('/admin/qr/checkin', 'QrController::checkin');

/*
|--------------------------------------------------------------------------
| CRON JOBS
|--------------------------------------------------------------------------
*/
$routes->cli('cron/price/rollover', 'CronPriceController::rollover');
$routes->get('cron/price/rollover', 'CronPriceController::rollover'); // Untuk testing via browser

/*
|--------------------------------------------------------------------------
| DEFAULT REDIRECT
|--------------------------------------------------------------------------
*/
$routes->get('/', function () {
    return redirect()->to('/login');
});
