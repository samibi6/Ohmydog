<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return Inertia::render('Admin/User/Index', [
            'users' => $users
        ]);
    }

    public function store()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
