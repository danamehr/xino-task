<?php

namespace App\Console\Commands;

use App\Modules\Subscription\Jobs\CacheSectionsJob;
use App\Modules\User\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InitiateApplicationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run application prerequisites.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('Running migrations...');
        Artisan::call('migrate --force');
        $this->info('Migrations ran successfully.');

        $this->line('Running seeders...');
        Artisan::call('db:seed --force');
        $this->info('Seeders ran successfully.');

        $this->line('Caching Sections...');
        dispatch_sync(new CacheSectionsJob());
        $this->info('Sections cached successfully.');

        /** @var User $user */
        $user = User::query()->first();
        $token = $user->createToken('postman-token')->plainTextToken;

        $this->line('Here is an auth token which you can use to call endpoints using postman:');
        $this->info($token);
    }
}
