<?php
Route::group(['middleware' => 'web'], function () {
    Route::name('shibboleth-login')->get('/shibboleth-login', [\Jhu\Wse\LaravelShibboleth\Controllers\ShibbolethController::class, 'login']);
    Route::name('shibboleth-authenticate')->get('/shibboleth-authenticate', [\Jhu\Wse\LaravelShibboleth\Controllers\ShibbolethController::class, 'idpAuthenticate']);
    Route::name('shibboleth-logout')->get('/shibboleth-logout', [\Jhu\Wse\LaravelShibboleth\Controllers\ShibbolethController::class, 'destroy']);
});
