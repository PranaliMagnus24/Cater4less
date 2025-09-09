<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GiftCardShare;

class ExpireSharedGiftCards extends Command
{
    protected $signature = 'giftcards:expire-shared';
    protected $description = 'Expire pending shared gift cards whose 24h window has passed';

    public function handle(): int
    {
        $count = GiftCardShare::where('status', 'pending')
            ->whereNotNull('share_expires_at')
            ->where('share_expires_at', '<', now())
            ->update(['status' => 'expired']);

        $this->info("Expired {$count} shared gift cards.");
        return self::SUCCESS;
    }
}
