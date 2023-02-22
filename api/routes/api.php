<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', 'App\Http\Controllers\API\User\AuthController@login')->name('auth.login');
    Route::post('/logout', 'App\Http\Controllers\API\User\AuthController@logout')->middleware('auth:sanctum')->name('auth.logout');
});

Route::post('/users', 'App\Http\Controllers\API\User\UserController@store')->name('users.store');
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', 'App\Http\Controllers\API\User\UserController@index')->name('users.index');
    Route::patch('/', 'App\Http\Controllers\API\User\UserController@update')->name('users.update');
    Route::delete('/', 'App\Http\Controllers\API\User\UserController@destroy')->name('users.destroy');

    Route::get('/roles', 'App\Http\Controllers\API\User\RoleController@index')->name('users.roles.index');
    Route::patch('/{user}/roles', 'App\Http\Controllers\API\User\RoleController@update')->name('users.roles.update');
});

Route::prefix('organizations')->middleware('auth:sanctum')->group(function () {
    Route::get('/', 'App\Http\Controllers\API\OrganizationController@index')->name('organizations.index');
    Route::post('/', 'App\Http\Controllers\API\OrganizationController@store')->name('organizations.store');
    Route::patch('/{organization}', 'App\Http\Controllers\API\OrganizationController@update')->name('organizations.update');
    Route::delete('/{organization}', 'App\Http\Controllers\API\OrganizationController@destroy')->name('organizations.destroy');
});

Route::prefix('groups')->middleware('auth:sanctum')->group(function () {
    Route::get('/', '\App\Http\Controllers\API\Group\GroupController@index')->name('groups.index');
    Route::get('/{group}', '\App\Http\Controllers\API\Group\GroupController@show')->name('groups.show');
    Route::post('/', '\App\Http\Controllers\API\Group\GroupController@store')->name('groups.store');
    Route::patch('/{group}', '\App\Http\Controllers\API\Group\GroupController@update')->name('groups.update');
    Route::delete('/{group}', '\App\Http\Controllers\API\Group\GroupController@destroy')->name('groups.destroy');

    Route::get('/{group}/users', '\App\Http\Controllers\API\Group\UserController@index')->name('groups.users.index');
    Route::post('/{group}/users/', '\App\Http\Controllers\API\Group\UserController@store')->name('groups.users.store');
    Route::delete('/{group}/users/{user}', '\App\Http\Controllers\API\Group\UserController@destroy')->name('groups.users.destroy');

    Route::get('/users/roles', 'App\Http\Controllers\API\Group\UserRoleController@index')->name('groups.users.roles.index');
    Route::patch('/{group}/users/{user}/roles', 'App\Http\Controllers\API\Group\UserRoleController@update')->name('groups.users.roles.update');
});
