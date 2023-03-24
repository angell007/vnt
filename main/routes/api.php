<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\VotanteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::prefix("auth")->group(
  function () {
    Route::post("login", [AuthController::class, 'login'])->name('login');
    Route::post("register", [AuthController::class, "register"]);
    Route::middleware("auth.jwt")->group(function () {
      Route::post("logout", [AuthController::class, "logout"]);
      Route::post("refresh", [AuthController::class, "refresh"]);
      Route::post("me", [AuthController::class, "me"]);
      Route::get("renew", [AuthController::class, "renew"]);
      Route::get("change-password", [
        AuthController::class,
        "changePassword",
      ]);
    });
  }
);

Route::middleware('auth.jwt')->prefix("cargos")->group(
  function () {
    Route::get("/", [CargoController::class, 'index'])->name('index');
    Route::get("/forselect", [CargoController::class, 'forSelect'])->name('forselect');
    Route::get("/{cargo}", [CargoController::class, 'get'])->name('get');
    Route::post("/", [CargoController::class, 'store'])->name('store');
    Route::post("/{cargo}", [CargoController::class, 'update'])->name('update');
    Route::delete("/{cargo}", [CargoController::class, 'destroy'])->name('delete');
  }
);

Route::middleware('auth.jwt')->prefix("votantes")->group(
  function () {
    Route::get("/", [VotanteController::class, 'index'])->name('index');
    Route::get("/forselect", [VotanteController::class, 'forSelect'])->name('forselect');
    Route::get("/{votante}", [VotanteController::class, 'get'])->name('get');
    Route::post("/", [VotanteController::class, 'store'])->name('store');
    Route::post("/{votante}", [VotanteController::class, 'update'])->name('update');
    Route::delete("/{votante}", [VotanteController::class, 'destroy'])->name('delete');
  }
);


// Route::get('preview', function () {
//   $inventory = Inventory::find(2);
//   $user = $inventory->user->name;
//   $data = ['email' => 'mdgrisalez@misena.edu.co', 'vendor' => $inventory->user->name, 'code' => $inventory->id];
//   Mail::to($data['email'])->send(new EmailInventory($data));
//   return response()->json($user);
// });



Route::get('/clear-cache', function () {
  $exitCode = Artisan::call('config:clear');
  $exitCode = Artisan::call('cache:clear');
  $exitCode = Artisan::call('config:cache');
  return 'DONE'; //Return anything
});
