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
    public function getArticles(int $perPage = 10): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 0);
    }

    /**
     * @inheritDoc
     */
    public function getArticlesByCategory(string $slug, int $perPage = 10): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 0);
    }

    /**
     * @inheritDoc
     */
    public function countCategories(): Collection
    {
        return new Collection();
    }

    /**
     * @inheritDoc
     */
    public function getPages(int $perPage = 10): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 0);
    }

    /**
     * @inheritDoc
     */
    public function getTemplates(int $perPage = 10): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 0);
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
    public function getHeaderContent(): ?object
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getFooterContent(): ?object
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