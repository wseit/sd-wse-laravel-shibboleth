<?php
Route::group(['middleware' => 'web'], function () {
    Route::get('emulated/idp', [\Jhu\Wse\LaravelShibboleth\Controllers\ShibbolethController::class, 'emulateIdp']);
    Route::post('emulated/idp', [\Jhu\Wse\LaravelShibboleth\Controllers\ShibbolethController::class, 'emulateIdp']);
    Route::get('emulated/login', [\Jhu\Wse\LaravelShibboleth\Controllers\ShibbolethController::class, 'emulateLogin']);
    Route::get('emulated/logout', [\Jhu\Wse\LaravelShibboleth\Controllers\ShibbolethController::class, 'emulateLogout']);
});
