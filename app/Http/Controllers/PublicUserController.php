<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicUserController extends Controller
{
    public function __invoke($userName)
    {
        $user = User::where('username', $userName)->firstOrFail();

        return view('public-user', ['user' => $user]);
    }
}
