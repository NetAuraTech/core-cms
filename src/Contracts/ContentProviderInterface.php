<?php

namespace Netauratech\CoreCms\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ContentProviderInterface
{
    /**
     * Retrieves all articles.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getArticles(int $perPage = 10): LengthAwarePaginator;

    /**
     * Retrieves articles from category.
     *
     * @param string $slug
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getArticlesByCategory(string $slug, int $perPage = 10): LengthAwarePaginator;

    /**
     * Returns categories with the number of articles
     *
     * @return Collection
     */
    public function countCategories(): Collection;

    /**
     * Retrieves all pages.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPages(int $perPage = 10): LengthAwarePaginator;

    /**
     * Retrieves all template.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getTemplates(int $perPage = 10): LengthAwarePaginator;

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
    public function getContentBySlug(string $slug): ?object;

    /**
     * Transforms a string containing a list of names
     * separated by commas into a collection of corresponding Eloquent objects,
     * or an empty array.
     *
     * @param string|null $value A string containing item names separated by commas.
     * @param string $model The name of the target model.
     * @return Collection|array A collection of corresponding Eloquent objects,
     * or an empty array if the string is empty, or if no elements match the given model.
     */
    public function reverseTransform(?string $value, string $model): Collection|array;
}