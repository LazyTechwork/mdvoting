<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;

class UserController extends Controller
{
    public function loginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

    }

    public function registerPage()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {

    }
}
