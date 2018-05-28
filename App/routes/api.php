<?php
/*
|--------------------------------------------------------------------------
| Api routing
|--------------------------------------------------------------------------
|
| Register it all your api routes
|
*/

$app->group('', function () {
  $app->get('/', [\App\Controllers\PagesController::class, 'getHome']);
  $app->get('/scan', [\App\Controllers\OpenGraphScannerController::class, 'getScan']);
})->add(new \App\Middlewares\CorsMiddleware());
