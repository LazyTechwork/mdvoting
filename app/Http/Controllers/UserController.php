<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Request;

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
            'username.exists' => 'Такой пользователь не найден в нашей базе данных',
            'password.required' => 'Вы забыли ввести свой пароль'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator)->withInput($request->all());

        $user = User::where('username', $request->get('username'))->first();
    }

    public function registerPage()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {

    }
}
