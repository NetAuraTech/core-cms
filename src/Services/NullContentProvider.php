<?php

namespace Netauratech\CoreCms\Services;

use Illuminate\Support\Collection;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;

class NullContentProvider implements ContentProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getBlogPosts(): Collection
    {
        return new Collection();
    }

    /**
     * @inheritDoc
     */
    public function getPagePosts(): Collection
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
}