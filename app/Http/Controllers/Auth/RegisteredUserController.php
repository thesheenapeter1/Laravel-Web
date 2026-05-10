<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, 'regex:/\.com$/i'],
            'phone' => ['required', 'regex:/^[0-9]{10}$/'],
            'address' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::min(9)->mixedCase()->numbers()->symbols()],
        ], [
            'email.regex' => 'Email must end with .com',
            'phone.regex' => 'Contact number must be exactly 10 digits.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => 2, // Default to Customer
        ]);

        event(new Registered($user));

        // Send Email Notification
        try {
            \Illuminate\Support\Facades\Mail::raw("New Customer Registered!\n\nName: {$user->name}\nEmail: {$user->email}\nPhone: {$user->phone}\nAddress: {$user->address}", function ($mail) {
                $mail->to('thesheenapeter2006@gmail.com')
                     ->subject('New Customer Registered - Aura by Kiyara');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
