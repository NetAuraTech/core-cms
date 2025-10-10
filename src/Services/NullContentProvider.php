<?php

namespace Netauratech\CoreCms\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;

class NullContentProvider implements ContentProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getContents(string $type, ?int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 0);
    }

    /**
     * @inheritDoc
     */
    public function getContentsByCategory(string $type, string $slug, ?int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 0);
    }

    /**
     * @inheritDoc
     */
    public function countCategories(string $type): Collection
    {
        return new Collection();
    }

    /**
     * @inheritDoc
     */
    public function getContentById(int $id): ?object
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getContentBySlug(string $slug): ?object
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform(?string $value, string $model): Collection|array
    {
        return [];
    }
}