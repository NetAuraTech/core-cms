<?php

namespace Netauratech\CoreCms\Services;

use Illuminate\Support\Collection;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;

class NullContentProvider implements ContentProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getArticles(): Collection
    {
        return new Collection();
    }

    /**
     * @inheritDoc
     */
    public function getPages(): Collection
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
}