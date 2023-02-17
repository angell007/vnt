<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Mail\EmailInventory;
use App\Models\Inventory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

// use SimpleSoftwareIO\QrCode\Facades\QrCode;


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


Route::get('preview', function () {

  $inventory = Inventory::find(2);
  $user = $inventory->user->name;
  $data = ['email' => 'mdgrisalez@misena.edu.co', 'vendor' => $inventory->user->name, 'code' => $inventory->id];
  Mail::to($data['email'])->send(new EmailInventory($data));

  return response()->json($user);
});

Route::get('qr-code-g', function () {

  // $image = \QrCode::backgroundColor(255, 255, 0)->color(255, 0, 127)
  //   ->format('png')
  //   // ->merge(public_path('/imgs/inventory.png'), 0.3, true)
  //   ->size(500)
  //   ->generate('ItSolutionStuff.com',  public_path('/imgs/' . '123.png'));
  // return response($image)->header('Content-type', 'image/png');
});


Route::get('/clear-cache', function () {

  $exitCode = Artisan::call('config:clear');

  $exitCode = Artisan::call('cache:clear');

  $exitCode = Artisan::call('config:cache');

  return 'DONE'; //Return anything

});
