<?php

namespace Netauratech\CoreCms\Contracts;

use Illuminate\Support\Collection;

interface ContentProviderInterface
{
    /**
     * Retrieves all articles.
     *
     * @return Collection
     */
    public function getArticles(): Collection;

    /**
     * Retrieves all pages.
     *
     * @return Collection
     */
    public function getPages(): Collection;

    /**
     * Retrieves a content item by its ID.
     *
     * @param int $id
     * @return object|null The content template or null if not found.
     */
    public function getContentById(int $id): ?object;

    /**
     * Retrieves a content item by its slug.
     *
     * @param string $slug
     * @return object|null The content model or null if not found.
     */
    public function getContentBySlug(string $slug): ?object; // NOUVELLE MÉTHODE

    /**
     * Retrieves the header content (Content type ‘header’).
     *
     * @return object|null The header content model or null if not found.
     */
    public function getHeaderContent(): ?object;

    /**
     * Retrieves the footer content (Content of type ‘footer’).
     *
     * @return object|null The footer content model or null if not found.
     */
    public function getFooterContent(): ?object;
}