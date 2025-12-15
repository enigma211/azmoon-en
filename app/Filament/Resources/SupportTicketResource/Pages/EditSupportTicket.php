<?php

namespace App\Filament\Resources\SupportTicketResource\Pages;

use App\Filament\Resources\SupportTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupportTicket extends EditRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If replied, change status to answered
        if (!empty($data['admin_reply'])) {
            // Save reply in replies table
            \App\Models\TicketReply::create([
                'support_ticket_id' => $this->record->id,
                'user_id' => null, // admin
                'message' => $data['admin_reply'],
                'is_admin' => true,
            ]);
            
            $data['status'] = 'answered';
            
            // If not replied before, set replied_at
            if (empty($this->record->replied_at)) {
                $data['replied_at'] = now();
            }
            
            // Clear admin_reply as it is saved in replies table
            $data['admin_reply'] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
