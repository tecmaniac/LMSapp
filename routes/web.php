<?php

use App\Models\costum\dbCreate;
use Illuminate\Support\Facades\Route;

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


Route::get('/dbCreate', function () {
    $db = new dbCreate();
    $db->dbMake(false);
    dd("Jalan");
    // return "yay";
    // return redirect('/');
});

Route::get('/', function () {
    return view('welcome');
});