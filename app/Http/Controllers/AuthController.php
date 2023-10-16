<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    function login(Request $request)
    {
        $checkUser = Auth::attempt(['username' => $request->input('username'), 'password' => $request->input('password')]);
        if($checkUser){
            $token =  Auth::user()->createToken(Str::uuid())->plainTextToken;
            return response([
                'success' => true,
                'message' => 'login successfully',
                '_token' => $token
            ]);
        }
        else{
            return response([
                'success' => false,
                'message' => "username or password not match"
            ], 401);
        }
    }


    function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3|max:15|string',
            'username' => 'required|unique:users|min:3|max:10',
            'password' => 'required|min:4|max:8',
            'confirm_password' => 'required|same:password',
        ]);


        if ($validator->fails()){
            return response([
                'success' => false,
                'message' => 'validation error!',
                'errors' => validationFormatter($validator->errors())
            ], 422);
        }
        else{
            DB::beginTransaction();
           try{
               $user = User::create([
                   'name' => $request->input('name'),
                   'username' => $request->input('username'),
                   'password' => Hash::make($request->input('password')),
               ]);

               if($request->hasFile('image')){

                   $user->
                   addMediaFromRequest('image')->toMediaCollection('avatar')
                   ->withCustomProperties(['mime-type' => 'image/jpeg']);
               }

               DB::commit();
               return response([
                   'success' => true,
                   'message' => 'registration successfully.',
                   '_token' => $user->createToken(Str::uuid())->plainTextToken
               ]);
           }
           catch (\Exception $exception){
                DB::rollBack();
                return response([
                    'status' => false,
                    'message' => $exception->getMessage()
                ], 500);
           }

        }
    }
}
