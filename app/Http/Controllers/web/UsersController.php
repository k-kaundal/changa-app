<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{


    function login()
    {
        return view('auth.login');
    }


    function check_login(LoginRequest $request)
    {
        try {
            $credentials = $request->except(['_token']);
            if (Auth::attempt($credentials)) {
                if(Auth::user()->user_type == 1){
                    return redirect()->route('dashboard');
                }
                else{
                    return back()->withInput()->with('error','Credential not matched');
                }
                // return response()->json(['message' => 'You have successfull login', 'redirect' => route('dashboard'), 'status' => true]);
            } else {
                return back()->with('error','Credential not matched');
                // return response()->json(['message' => 'No user found', 'status' => false]);
            }
        } catch (\Throwable $e) {
            return $e->getMessage();
            // return response()->json(['message' => json_encode($e->getMessage()), 'status' => config('commonStatus.INACTIVE')]);
        }
    }

    function registration()
    {
        return view('auth.registration');
    }


}