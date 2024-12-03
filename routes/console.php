<?php

use App\Console\Commands\HandleExpiredSubscriptionsCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(HandleExpiredSubscriptionsCommand::class)->hourly();
