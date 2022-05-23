<?php

namespace App\Http\Controllers;

use App\Models\User;

class PublicUserController extends Controller
{
    public function __invoke($userName)
    {
        $user = User::where('username', $userName)->firstOrFail();

        return view('public-user', ['user' => $user]);
    }
}
