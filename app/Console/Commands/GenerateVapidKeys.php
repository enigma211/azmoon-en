<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vapid:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate VAPID keys for Web Push Notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('تولید VAPID keys...');
        
        try {
            $keys = VAPID::createVapidKeys();
            
            $this->newLine();
            $this->info('✅ VAPID keys با موفقیت تولید شدند!');
            $this->newLine();
            
            $this->line('این کلیدها را به فایل .env اضافه کنید:');
            $this->newLine();
            
            $this->line('VAPID_PUBLIC_KEY=' . $keys['publicKey']);
            $this->line('VAPID_PRIVATE_KEY=' . $keys['privateKey']);
            
            $this->newLine();
            $this->warn('⚠️ کلید خصوصی را در جای امن نگه دارید و در Git قرار ندهید!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ خطا در تولید VAPID keys: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
