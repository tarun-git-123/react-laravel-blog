<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\TempImageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('get-all-blogs',[BlogController::class,'index']);
Route::get('get-blog/{id}',[BlogController::class,'show']);
Route::post('blogs',[BlogController::class,'store']);
Route::post('save-temp-image',[TempImageController::class,'store']);
Route::put('update-blog/{id}',[BlogController::class,'update']);
Route::delete('delete-blog/{id}',[BlogController::class,'destroy']);