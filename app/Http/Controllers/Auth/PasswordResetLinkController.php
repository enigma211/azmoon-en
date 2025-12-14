<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetPasswordCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset email (code) request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset code request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $request->string('email'))->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('We can\'t find a user with that email address.')]);
        }

        // Generate a 6-digit numeric code.
        $code = (string) random_int(100000, 999999);

        // Store a hashed version of the code in password_reset_tokens with 15-minute TTL.
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        // Send the code via email.
        $user->notify(new ResetPasswordCodeNotification($code));

        return redirect()
            ->route('password.code.verify.form', ['email' => $user->email])
            ->with('status', __('We have emailed your password reset code.'));
    }

    /**
     * Show the form where user enters the 6-digit code.
     */
    public function showCodeVerifyForm(Request $request): View
    {
        return view('auth.verify-reset-code', [
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Verify the submitted 6-digit code and, if valid, allow the user to set a new password.
     */
    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->string('email'))
            ->first();

        if (! $record) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('We can\'t find a reset request for that email address.')]);
        }

        // Check expiry (15 minutes).
        if (Carbon::parse($record->created_at)->lt(now()->subMinutes(15))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['code' => __('This reset code has expired. Please request a new one.')]);
        }

        if (! Hash::check($request->string('code'), $record->token)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['code' => __('The reset code you entered is invalid.')]);
        }

        // Code is valid – remove the token so it can\'t be reused.
        DB::table('password_reset_tokens')
            ->where('email', $request->string('email'))
            ->delete();

        // Remember which email is allowed to reset password.
        $request->session()->put('password_reset_email', $request->string('email'));

        return redirect()->route('password.reset.form');
    }
}
