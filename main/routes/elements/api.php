<?php

use App\Http\Controllers\ElementController;
use Illuminate\Support\Facades\Route;

Route::post("register", [ElementController::class, "register"]);
Route::get("changuecheked", [ElementController::class, "changuecheked"]);
Route::post("first", [ElementController::class, "first"]);
Route::post("update", [ElementController::class, "update"]);
Route::get("index", [ElementController::class, "index"]);
Route::get("index2", [ElementController::class, "index2"]);
// Route::get("index/{store}", [ElementController::class, "index"]);
Route::get("saveinventory/{store}", [ElementController::class, "saveinventory"]);
Route::get("getqr/{element}", [ElementController::class, "getQr"]);
Route::get("pdfdownload/{id}", [ElementController::class, "downloadPdf"]);
Route::get("changuestatus", [ElementController::class, "changuestatus"]);
// Route::get("{element}", [ElementController::class, "get"]);
Route::post("/getdata", [ElementController::class, "get"]);
Route::delete("{element}", [ElementController::class, "delete"]);
