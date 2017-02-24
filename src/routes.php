<?php

// Login Route (Shibboleth)
Route::get('/idp', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@login');
// Logout Route (Shibboleth and Local)
Route::get('/logout', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@destroy');
// Shibboleth IdP Callback
Route::get('/authorize', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@idpAuthorize');

// Login Callback (Emulated)
Route::get('emulated/idp', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateIdp');
// Login Callback (Emulated)
Route::post('emulated/idp', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateIdp');
// Login Route (Emulated)
Route::get('emulated/login', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateLogin');
// Logout Route (Emulated)
Route::get('emulated/logout', 'StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@emulateLogout');
