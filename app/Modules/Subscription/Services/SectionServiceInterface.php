<?php

namespace App\Modules\Subscription\Services;

use App\Modules\Subscription\Models\Section;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

interface SectionServiceInterface
{
    public function getSections(User $user): Collection;

    public function getSection(string $slug): Section;
}
