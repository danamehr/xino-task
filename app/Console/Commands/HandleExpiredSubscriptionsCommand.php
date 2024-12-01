<?php

namespace App\Console\Commands;

use App\Modules\User\Models\User;
use Illuminate\Console\Command;

class HandleExpiredSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:handle-expired-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired subscriptions that have not been renewed.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Subscriptions that haven't been renewed within 1 day after expiration, couldn't be renewed anymore.
        User::query()
            ->whereNotNull('plan_expires_at')
            ->where('plan_expires_at', '<', now()->subDay())
            ->update([
                'plan_id' => null,
                'plan_expires_at' => null,
            ]);
    }
}
