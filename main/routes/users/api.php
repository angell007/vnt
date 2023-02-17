<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::delete("{user}", [UserController::class, "delete"]);
Route::post("register", [UserController::class, 'register'])->name('register');
Route::get("index", [UserController::class, 'index'])->name('index');
Route::get("myowners/{id}", [InventoryController::class, "myowners"]);
Route::post("update", [UserController::class, "update"]);
Route::post("getdata", [UserController::class, "getdata"]);
