<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view (after code verification).
     */
    public function create(Request $request): View
    {
        if (! $request->session()->has('password_reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'confirmed',
                // At least 8 characters, must contain letters and numbers.
                Rules\Password::min(8)->letters()->numbers(),
            ],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if (! $user) {
            $request->session()->forget('password_reset_email');

            return redirect()->route('password.request')
                ->withErrors(['email' => __('We can\'t find a user with that email address.')]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        // Clean up any remaining reset tokens and session flag.
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        $request->session()->forget('password_reset_email');

        return redirect()->route('login')->with('status', __('Your password has been reset.'));
    }
}
