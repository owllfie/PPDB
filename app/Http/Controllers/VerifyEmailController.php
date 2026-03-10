<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function show()
    {
        return redirect()->intended('/index');
    }

    public function verify(Request $request)
    {
        return redirect()->route('login');
    }

    public function resend()
    {
        return redirect()->route('login');
    }
}
