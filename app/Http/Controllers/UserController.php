<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('users', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'full_name' => 'required'
        ]);

        User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'role' => $request->role
        ]);

        return redirect('/users');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required',
            'full_name' => 'required',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $user = User::findOrFail($id);

        $avatarPath = $user->avatar;

        if ($request->hasFile('avatar')) {

            $file = $request->file('avatar');

            $filename = time() . '.' .
                $file->getClientOriginalExtension();
            $image = Image::read($file)
                ->cover(150, 150);

            Storage::disk('public')
                ->put(
                    'avatars/' . $filename,
                    (string) $image->encode()
                );

            $avatarPath = 'avatars/' . $filename;
        }
        $user->update([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'role' => $request->role,
            'avatar' => $avatarPath
        ]);

        return redirect('/users');
    }
}