<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;

class UserController extends Controller
{
    public function loginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'username' => ['required', 'string', 'exists:users,username'],
            'password' => ['required', 'string']
        ], [
            'username.required' => 'Введите имя пользователя',
            'username.exists' => 'Логин или пароль введены неверно',
            'password.required' => 'Вы забыли ввести свой пароль'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator)->withInput($request->all());

        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials))
            return redirect(RouteServiceProvider::HOME);
        else
            return redirect()->back()->withErrors(new MessageBag(['username' => 'Логин или пароль введены неверно']))->withInput($request->all());
    }

    public function registerPage()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = validator($request->all(), [
            'username' => ['required', 'string', 'unique:users,username'],
            'password' => ['required', 'string', 'min:4', 'confirmed']
        ], [
            'username.required' => 'Введите имя пользователя',
            'username.unique' => 'Пользователь уже зарегистрирован',
            'password.required' => 'Вы забыли ввести свой пароль',
            'password.confirmed' => 'Пароль должен быть подтверждён',
            'password.min' => 'Минимальная длина пароля - 4'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator)->withInput($request->all());

        $credentials = $request->only('username', 'password');
        $credentials['password'] = Hash::make($credentials['password']);
        User::create($credentials);
        if (Auth::attempt($credentials))
            return redirect(RouteServiceProvider::HOME);
        else
            return redirect(RouteServiceProvider::HOME);
    }
}
