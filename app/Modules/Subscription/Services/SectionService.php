<?php

namespace App\Modules\Subscription\Services;

use App\Modules\Subscription\Jobs\CacheSectionsJob;
use App\Modules\Subscription\Models\Section;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SectionService implements SectionServiceInterface
{
    public function getSections(User $user): Collection
    {
        // In the current task, the sections are not considered to be a large dataset. That's why we're caching
        // the whole table and also not using pagination. In other cases, the cache policy could be different.
        // We could also use RedisSearch to just have one copy of the data, but that's out of the scope of this task.

        $sections = collect();

        for ($i = 1; $i <= $user->plan->level; $i++) {
            if (! $serializedSections = Cache::get("subscriptions.section-levels.{$i}")) {
                dispatch(new CacheSectionsJob);

                return Section::query()->where('required_level', '<=', $user->plan->level)->get();
            }

            foreach (unserialize($serializedSections) as $section) {
                $sections->add($section);
            }
        }

        return $sections;
    }

    public function getSection(string $slug): Section
    {
        if ($section = Cache::get("subscriptions.sections.{$slug}")) {
            return unserialize($section);
        }

        $section = Section::query()->where('slug', $slug)->firstOrFail();

        // In a real-world scenario, if any changes happen to the existing sections data, either in the code
        // or from the admin panel, we'll need to regenerate the caches using its job immediately. So the following
        // lines do not get reached out in a normal situation, but still we handle it to prevent further issues.

        dispatch(new CacheSectionsJob);

        return $section;
    }
}
