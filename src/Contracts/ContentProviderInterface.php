<?php

namespace Netauratech\CoreCms\Contracts;

use Illuminate\Support\Collection;

interface ContentProviderInterface
{
    /**
     * Retrieves all blog posts.
     *
     * @return Collection
     */
    public function getBlogPosts(): Collection;

    /**
     * Retrieves all pages.
     *
     * @return Collection
     */
    public function getPagePosts(): Collection;

    /**
     * Retrieves a content item by its ID.
     *
     * @param int $id
     * @return object|null The content template or null if not found.
     */
    public function getContentById(int $id): ?object;
}