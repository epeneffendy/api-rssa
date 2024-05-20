<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

$routeService = App::make('App\Services\RouteService');
$helper = App::make('App\Helpers\Helper');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

 Route::get('/', function () {
     return view('welcome');
 });

Route::group(['prefix' => 'bankjatim'], function () {
    Route::post('/generate-qris-jatim', 'App\Http\Controllers\Qris\v1\QrisJatimController@GenerateQris');
    Route::post('/check-status-qris-jatim', 'App\Http\Controllers\Qris\v1\QrisJatimController@CheckStatusQris');
    Route::post('/PaymentQr', 'App\Http\Controllers\Qris\v1\QrisJatimController@PaymentQris');

    Route::post('/virtual-account-full', 'App\Http\Controllers\Va\v1\VirtualAccountController@CreateVirtualAccountFull');
    Route::post('/callback-va', 'App\Http\Controllers\Va\v1\VirtualAccountController@CallbackVa');
});

Route::group(['prefix' => 'poct'], function () {
    Route::post('/list-pemeriksaan', 'App\Http\Controllers\Poct\PemeriksaanController@ListPemeriksaan');
    Route::post('/rekap-pemeriksaan', 'App\Http\Controllers\Poct\PemeriksaanController@RekapPemeriksaan');
    Route::post('/signature', 'App\Http\Controllers\Poct\PemeriksaanController@Signature');
    Route::get('/token', 'App\Http\Controllers\Poct\PemeriksaanController@Token');
});
