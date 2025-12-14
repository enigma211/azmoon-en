<?php

namespace App\Livewire;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SupportTicketsPage extends Component
{
    public $subject = '';
    public $message = '';
    public $showCreateForm = false;
    public $selectedTicket = null;
    public $replyMessage = '';

    protected $rules = [
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:2000',
    ];

    protected $messages = [
        'subject.required' => 'Ticket subject is required',
        'subject.max' => 'Subject must not exceed 255 characters',
        'message.required' => 'Ticket message is required',
        'message.max' => 'Message must not exceed 2000 characters',
    ];

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        if (!$this->showCreateForm) {
            $this->reset(['subject', 'message']);
            $this->resetValidation();
        }
    }

    public function createTicket()
    {
        $this->validate();

        // Create Ticket
        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => 'pending',
        ]);

        session()->flash('success', 'Your ticket has been created successfully. Ticket Number: ' . $ticket->ticket_number);
        
        $this->reset(['subject', 'message', 'showCreateForm']);
        $this->resetValidation();
    }

    public function viewTicket($ticketId)
    {
        $this->selectedTicket = SupportTicket::with('replies.user')
            ->where('user_id', Auth::id())
            ->findOrFail($ticketId);
        $this->replyMessage = '';
    }

    public function closeTicketView()
    {
        $this->selectedTicket = null;
        $this->replyMessage = '';
    }

    public function sendReply()
    {
        $this->validate([
            'replyMessage' => 'required|string|max:2000',
        ], [
            'replyMessage.required' => 'Reply message is required',
            'replyMessage.max' => 'Reply message must not exceed 2000 characters',
        ]);

        TicketReply::create([
            'support_ticket_id' => $this->selectedTicket->id,
            'user_id' => Auth::id(),
            'message' => $this->replyMessage,
            'is_admin' => false,
        ]);

        // Change status to pending for admin notification
        $this->selectedTicket->update(['status' => 'pending']);

        session()->flash('reply_success', 'Your reply has been sent successfully.');
        
        // Reload ticket with replies
        $this->selectedTicket = SupportTicket::with('replies.user')
            ->findOrFail($this->selectedTicket->id);
        
        $this->replyMessage = '';
    }

    public function render()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.support-tickets-page', [
            'tickets' => $tickets,
        ])->layout('layouts.app');
    }
}
