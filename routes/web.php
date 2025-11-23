<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ReceiptController
};
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

Route::get('/receipt/{id}', [ReceiptController::class, 'printPdf'])->name('receipt.show');


Route::get('/vue', function () {
    return view('vue');
});



Route::get('/vue/{any?}', function () {
    return view('vue');
})->where('any', '.*');
