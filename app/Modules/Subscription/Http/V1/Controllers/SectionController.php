<?php

namespace App\Modules\Subscription\Http\V1\Controllers;

use App\Modules\Shared\Http\V1\Controllers\Controller;
use App\Modules\Subscription\Http\V1\Resources\SectionResource;
use App\Modules\Subscription\Models\Section;
use App\Modules\Subscription\Services\SectionServiceInterface;

class SectionController extends Controller
{
    public function __construct(protected SectionServiceInterface $sectionService)
    {
    }

    public function index()
    {
        $this->authorize('viewAny', Section::class);

        return SectionResource::collection($this->sectionService->getSections(auth()->user()));
    }

    public function show(string $slug)
    {
        try {
            $section = $this->sectionService->getSection($slug);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $modelNotFoundException) {
            return response(['message' => __('subscription::messages.section_not_found')], status: 404);
        }

        $this->authorize('view', $section);

        return SectionResource::make($section);
    }
}
