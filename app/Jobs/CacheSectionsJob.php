<?php

namespace App\Jobs;

use App\Modules\Subscription\Models\Section;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class CacheSectionsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::flush();

        // We do not set a ttl for section caches because they are not supposed to change in this project, and we always need them.
        $sections = Section::all();

        foreach ($sections as $section) {
            Cache::forever("subscriptions.sections.{$section->slug}", serialize($section));
        }

        for ($i = 1; $i <= 3; $i++) {
            Cache::forever(
                "subscriptions.section-levels.{$i}",
                serialize($sections->where('required_level', $i))
            );
        }
    }
}
