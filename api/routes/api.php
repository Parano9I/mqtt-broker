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

Route::prefix('organizations')->middleware('auth:sanctum')->group(function () {
    Route::get('/', 'App\Http\Controllers\API\OrganizationController@index')->name('organization.index');
    Route::post('/', 'App\Http\Controllers\API\OrganizationController@store')->name('organization.store');
    Route::patch('/{organization}', 'App\Http\Controllers\API\OrganizationController@update')->name('organization.update');
    Route::delete('/{organization}', 'App\Http\Controllers\API\OrganizationController@destroy')->name('organization.destroy');
});

Route::prefix('groups')->middleware('auth:sanctum')->group(function () {
    Route::get('/', '\App\Http\Controllers\API\Group\GroupController@index')->name('group.index');
    Route::get('/{group}', '\App\Http\Controllers\API\Group\GroupController@show')->name('group.show');
    Route::post('/', '\App\Http\Controllers\API\Group\GroupController@store')->name('group.store');
    Route::patch('/{group}', '\App\Http\Controllers\API\Group\GroupController@update')->name('group.update');
    Route::delete('/{group}', '\App\Http\Controllers\API\Group\GroupController@destroy')->name('group.destroy');

    Route::get('/{group}/users', '\App\Http\Controllers\API\Group\UserController@index')->name('group.user.index');
    Route::post('/{group}/users/', '\App\Http\Controllers\API\Group\UserController@store')->name('group.user.store');
    Route::delete('/{group}/users/{user}', '\App\Http\Controllers\API\Group\UserController@destroy')->name('group.user.destroy');

    Route::get('/users/roles', 'App\Http\Controllers\API\Group\UserRoleController@index')->name('group.user.role.index');
    Route::patch('/{group}/users/{user}/roles', 'App\Http\Controllers\API\Group\UserRoleController@update')->name('group.users.role.update');
});
