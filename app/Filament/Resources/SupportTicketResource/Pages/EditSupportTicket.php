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
        // اگر پاسخ داده شده، وضعیت را به answered تغییر بده
        if (!empty($data['admin_reply'])) {
            // ذخیره پاسخ در جدول replies
            \App\Models\TicketReply::create([
                'support_ticket_id' => $this->record->id,
                'user_id' => null, // admin
                'message' => $data['admin_reply'],
                'is_admin' => true,
            ]);
            
            $data['status'] = 'answered';
            
            // اگر قبلاً پاسخ داده نشده، زمان پاسخ را ثبت کن
            if (empty($this->record->replied_at)) {
                $data['replied_at'] = now();
            }
            
            // پاک کردن admin_reply چون در جدول replies ذخیره شد
            $data['admin_reply'] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
