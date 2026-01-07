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
