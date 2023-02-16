<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', 'App\Http\Controllers\API\User\AuthController@login')->name('auth.login');
    Route::post('/logout', 'App\Http\Controllers\API\User\AuthController@logout')->middleware('auth:sanctum')->name('auth.logout');
});

Route::post('/users', 'App\Http\Controllers\API\User\UserController@store')->name('user.store');
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', 'App\Http\Controllers\API\User\UserController@index')->name('user.index');
    Route::patch('/', 'App\Http\Controllers\API\User\UserController@update')->name('user.update');
    Route::delete('/', 'App\Http\Controllers\API\User\UserController@destroy')->name('user.destroy');

    Route::get('/roles', 'App\Http\Controllers\API\User\RoleController@index')->name('users.roles.index');
    Route::patch('/{user}/roles', 'App\Http\Controllers\API\User\RoleController@update')->name('users.role.update');
});
