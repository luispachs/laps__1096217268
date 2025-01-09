<?php


use App\Http\Controllers\ContactoController;
use App\Http\Controllers\EntidadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::get("entities",[EntidadController::class,'index']);
Route::delete('entities',[EntidadController::class,'deleteMuch']);
Route::post("entities/store",[EntidadController::class,'store']);
Route::put("entities/{id}",[EntidadController::class,'update']);
Route::delete("entities/{id}",[EntidadController::class,'destroy']);

Route::get("contacts",[ContactoController::class,'index']);
Route::post("contacts/store",[ContactoController::class,'store']);
Route::delete("contact/{id}",[ContactoController::class,'destroy']);
Route::put("entities/{id}",[ContactoController::class,'update']);
Route::delete('entities',[ContactoController::class,'deleteMuch']);
