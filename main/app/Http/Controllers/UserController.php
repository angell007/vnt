<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Response;
use Illuminate\Support\Facades\Validator;
use Mail;
use App\Mail\Emailwelcome;

class UserController extends Controller
{
    use Response;

    public function register()
    {
        $validador = Validator::make(request()->all(), [
            'email' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            // 'document_type' => 'required|string|min:2',
            'user_type' => 'required|string|min:2',
            'document_number' => 'required|string|min:6',
        ]);

        if ($validador->fails()) {
            return $this->error($validador->errors(), 400);
        }

        $usuario = User::create([
            'email' => request('email'),
            // 'document_type' => request('document_type'),
            'user_type' => request('user_type'),
            'document_number' => request('document_number'),
            'name' => request('name'),
            'password' => bcrypt(request('document_number')),
        ]);

        $usuario->save();
        $receiver = ['email' => $usuario->email, 'pass' => $usuario->document_number];
        Mail::to($receiver['email'])->send(new Emailwelcome($receiver));
        
        // $token = $this->guard()->login($usuario);
        return $this->success(['message' => 'User created successfully'], 201);
        // return $this->success(['message' => 'User created successfully', 'token' => $token], 201);
    }
    
    public function index()
    {
         $items = User::select([
                    'email',
                    'id',
                    'user_type',
                    // 'document_type' => request('document_type'),
                    // 'document_number',
                    'name',
                    // 'password' => bcrypt(request('document_number')),
                ])->where('status', 1)->get();
        
        
                return $this->success($items, 201);
            }
            
    public function getdata()
    {
         $items = User::find(request()->get('id'), [
                    'id',
                    'email',
                    'user_type',
                    'name',
                ]);
        
        
                return $this->success($items, 201);
    }
            
     public function delete(User $user)
    {

        $user->status = $user->status == 1 ? 0 : 1;
        
        $user->save();

        return $this->success(['message' => 'user update successfully', 'item' => $user], 200);
    }
       
            
    public function update()
    {
        $validador = Validator::make(request()->all(), [
            'email' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'user_type' => 'required|string|min:2',
        ]);

        if ($validador->fails()) {
            return $this->error($validador->errors(), 400);
        }

        $usuario = User::find(request()->get('id'));
        
        $usuario->update([
            'email' => request('email'),
            'user_type' => request('user_type'),
            'name' => request('name'),
        ]);
            
        if(request()->get('password') != null) $usuario->password = bcrypt(request('document_number')) ;
        
        $usuario->save();
        \Log::info(' User update' );
        return $this->success(['message' => 'User updated successfully'], 201);
    }
    
}
