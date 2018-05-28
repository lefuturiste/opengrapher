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
  $this->get('/', [\App\Controllers\PagesController::class, 'getHome']);
  $this->get('/scan', [\App\Controllers\OpenGraphScannerController::class, 'getScan']);
})->add(new \App\Middlewares\CorsMiddleware());
