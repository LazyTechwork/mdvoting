<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function loginPage()
    {
        return view('auth.login');
    }

    public function registerPage()
    {
        return view('auth.register');
    }
}
