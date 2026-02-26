<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProfilePage extends Component
{
    public $subscription;
    public $availablePlans;
    public $daysRemaining = null;
    public $isExpired = false;
    public $isGuest = false;
    public $isPremium = false;
    public $guest_name;
    public $guest_email;
    public $guest_message;
    public $honeypot; // Honeypot field for bot protection

    // Auth properties
    public $showRegister = false;
    public $email;
    public $password;
    public $remember = true;

    public $name;
    public $register_email;
    public $register_password;
    public $register_password_confirmation;

    public function mount()
    {
        if (!Auth::check()) {
            $this->isGuest = true;
            return;
        }

        // Logged-in users are treated as free users; no subscription logic is used.
        $this->isGuest = false;
        $this->subscription = null;
        $this->availablePlans = collect();
        $this->daysRemaining = null;
        $this->isExpired = false;
        $this->isPremium = false;
    }

    public function submitGuestFeedback()
    {
        // Bot detection
        if (!empty($this->honeypot)) {
            return;
        }

        $this->validate([
            'guest_email' => 'required|email',
            'guest_message' => 'required|min:10',
        ]);

        // Rate limiting: 1 message per hour per IP
        $rateLimitKey = 'guest_feedback_' . request()->ip();
        if (cache()->has($rateLimitKey)) {
            session()->flash('warning', 'Too many requests. Please try again in an hour.');
            return;
        }

        \App\Models\SupportTicket::create([
            'guest_name' => $this->guest_name,
            'guest_email' => $this->guest_email,
            'message' => $this->guest_message,
            'subject' => 'Guest Feedback',
            'ticket_number' => \App\Models\SupportTicket::generateTicketNumber(),
            'guest_ip' => request()->ip(),
            'status' => 'pending',
            'user_id' => null, // explicitly guest
        ]);

        cache()->put($rateLimitKey, true, now()->addHour());

        $this->reset(['guest_name', 'guest_email', 'guest_message']);
        session()->flash('message', 'Thank you! Your feedback has been received.');
    }

    public function login()
    {
        // Bot detection: Honeypot
        if (!empty($this->honeypot)) {
            return;
        }

        // Rate limiting: 5 attempts per HOUR per IP
        $rateLimitKey = 'login_attempt_' . request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);
            session()->flash('warning', "Too many login attempts. Please try again in {$minutes} minutes.");
            return;
        }

        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            \Illuminate\Support\Facades\RateLimiter::clear($rateLimitKey);
            session()->regenerate();
            return redirect()->route('profile');
        }

        \Illuminate\Support\Facades\RateLimiter::hit($rateLimitKey, 3600); // Lock for 1 hour on failure
        $this->addError('email', trans('auth.failed'));
    }

    public function register()
    {
        // Bot detection: Honeypot
        if (!empty($this->honeypot)) {
            return;
        }

        // Rate limiting: 3 registrations per hour per IP
        $rateLimitKey = 'register_attempt_' . request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);
            session()->flash('warning', "Too many registration attempts. Please try again in {$minutes} minutes.");
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'register_email' => 'required|string|email|max:255|unique:users,email',
            'register_password' => ['required', 'string', 'min:8', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()],
        ]);

        $user = \App\Models\User::create([
            'name' => $this->name,
            'email' => $this->register_email,
            'username' => $this->register_email,
            'password' => \Illuminate\Support\Facades\Hash::make($this->register_password),
        ]);

        \Illuminate\Support\Facades\RateLimiter::hit($rateLimitKey, 3600); // Lock for 1 hour
        event(new \Illuminate\Auth\Events\Registered($user));

        Auth::login($user, $this->remember);

        return redirect()->route('profile');
    }

    public function toggleAuthMode()
    {
        $this->showRegister = !$this->showRegister;
        $this->resetErrorBag();
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('home');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.profile-page');
    }
}
