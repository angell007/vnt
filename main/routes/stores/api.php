<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::post("register", [StoreController::class, "register"]);
Route::post("update", [StoreController::class, "update"]);
Route::get("index", [StoreController::class, "index"]);
Route::post("getdata", [StoreController::class, "get"]);
Route::get("pdfdownload/{id}", [StoreController::class, "downloadPdf"]);
Route::delete("{store}", [StoreController::class, "delete"]);
