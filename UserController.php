<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password,
            'avatar' => $request->avatar,
            'job' => $request->job,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')->with('success', 'Succesfully stored user' . $request->first_name . ' ' . $request->last_name);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user = User::where('id', $user->id)->with('articles', 'role')->first();

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user = User::where('id', $user->id)->with('role')->first();
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password,
            'avatar' => $request->avatar,
            'job' => $request->job,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')->with('success', 'Succesfully updated user ' . $request->first_name . ' ' . $request->last_name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $name = $user->first_name . ' ' . $user->last_name;
        try {
            $user->delete();
        } catch (\PDOException $e) {
            return redirect()->back()->with('danger', "You can't delete user before related articles");
        }

        return redirect()->router('users.index')->with('success', 'Succesfully deleted user ' . $name);
    }
}