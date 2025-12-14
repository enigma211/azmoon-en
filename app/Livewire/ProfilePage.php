<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfilePage extends Component
{
    public $subscription;
    public $availablePlans;
    public $daysRemaining = null;
    public $isExpired = false;
    public $isGuest = false;
    public $isPremium = false;

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

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.profile-page')
            ->layout('layouts.app');
    }
}
