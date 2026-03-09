<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\WelcomeUserMail;
use App\Mail\EmailChangedMail;
use App\Mail\AccountDeletedMail;
use App\Mail\PasswordChangedMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required',
            'status'   => 'required',
        ]);


        $plainPassword = $request->password;

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'   => $request->mobile,
            'password' => bcrypt($request->password),
            'role'     => $request->role,
            'status'   => $request->status,
        ]);

        // Pass ONLY primitive data to queue
        $mailData = [
            'name'     => $user->name,
            'email'    => $user->email,
            'password' => $plainPassword,
        ];

        $clientCode = session('client_code'); 

        Mail::to($user->email)->later(
            now()->addSeconds(10),
            new WelcomeUserMail($mailData, $clientCode)
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully & email sent shortly.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'mobile'   => 'nullable|string|max:15',
            'role'     => 'required',
            'status'   => 'required',
        ]);

        $emailChanged = $request->email !== $user->email;
        $passwordChanged = filled($request->password);

        $oldEmail = $user->email;
        $newPassword = $request->password;

        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'   => $request->mobile,
            'role'     => $request->role,
            'status'   => $request->status,
            'password' => $passwordChanged ? Hash::make($newPassword) : $user->password,
        ]);

        $clientCode = session('client_code');

        // Email Changed
        if ($emailChanged) {
            $mailData = [
                'name'      => $user->name,
                'old_email' => $oldEmail,
                'new_email' => $user->email,
            ];

            Mail::to($user->email)
                ->later(now()->addSeconds(10), new EmailChangedMail($mailData, $clientCode));
        }

        // Password Changed
        if ($passwordChanged) {
            $mailData = [
                'name'        => $user->name,
                'email'       => $user->email,
                'new_password' => $newPassword,
            ];

            Mail::to($user->email)
                ->later(now()->addSeconds(10), new PasswordChangedMail($mailData, $clientCode));
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully & email sent shortly.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        $email = $user->email;
        $name  = $user->name;
        $clientCode = session('client_code');

        $user->delete();

        Mail::to($email)->later(
            now()->addSeconds(10),
            new AccountDeletedMail($name, $email, $clientCode)
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully & email sent shortly.');
    }
}
