<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin pages (HTML)
|--------------------------------------------------------------------------
| Loaded from bootstrap/app.php inside a group with the middleware
| web + auth + admin, the URL prefix /admin and the name prefix admin.
|
| The JSON APIs live in routes/web.php (/admin/services, /admin/bookings).
| The pages use different URLs so they never clash with those APIs.
*/

Route::view('manage-services', 'admin.services')->name('pages.services');
