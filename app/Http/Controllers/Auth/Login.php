<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class Login extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $creds = $request->validate([
            'name' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::attempt($creds, $request->boolean('remember'))){
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()
            ->withErrors(['name' => 'Either Username or Password is Wrong or both, kindly try again.'])
            ->onlyInput('name');
    }
}
