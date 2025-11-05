<?php

namespace Netauratech\CoreCms\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Netauratech\CoreCms\Models\Category;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Models\Tag;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;

class ContentProvider implements ContentProviderInterface
{
    /**
     * Retrieves all contents.
     *
     * @param string $type
     * @param ?int $perPage
     * @return LengthAwarePaginator
     */
    public function getContents(string $type, ?int $perPage = null): LengthAwarePaginator
    {
        return Content::where('type', $type)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->with('media')
            ->paginate($perPage);
    }

    /**
     * Retrieves contents from category.
     *
     * @param string $type
     * @param string $slug
     * @param ?int $perPage
     * @return LengthAwarePaginator
     */
    public function getContentsByCategory(string $type, string $slug, ?int $perPage = null): LengthAwarePaginator
    {
        return Content::where('type', $type)
            ->where('status', 'published')
            ->whereHas('categories', function ($query) use ($slug) {
                $query->where('categories.slug', $slug);
            })
            ->orderBy('published_at', 'desc')
            ->with('media')
            ->paginate($perPage);
    }

    /**
     * Returns categories with the number of articles
     *
     * @param string $type
     * @return Collection
     */
    public function countCategories(string $type): Collection
    {
        return DB::table('categories')
            ->leftJoin('content_category', 'categories.id', '=', 'content_category.category_id')
            ->leftJoin('contents', function ($join) use ($type) {
                $join->on('content_category.content_id', '=', 'contents.id')
                    ->where('contents.status', 'published')
                    ->where('contents.type', $type);
            })
            ->select(DB::raw('categories.*, COUNT(DISTINCT contents.id) as count'))
            ->groupBy('categories.id', 'categories.name', 'categories.slug', 'categories.created_at', 'categories.updated_at')
            ->orderBy('name')
            ->get();
    }

    /**
     * Retrieves a content item by its ID.
     *
     * @param int $id
     * @return object|null The content model or null if not found.
     */
    public function getContentById(int $id): ?object
    {
        return Content::find($id);
    }

    /**
     * Retrieves a content item by its slug.
     *
     * @param string $slug
     * @return object|null The content model or null if not found.
     */
    public function getContentBySlug(string $slug): ?object
    {
        return Content::where('slug', $slug)
            ->where('status', 'published')
            ->first();
    }

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
    public function reverseTransform(?string $value, string $model): Collection|array
    {
        if (empty($value)) {
            return [];
        }

        $versions = [];
        $tags = explode(',', $value);
        foreach ($tags as $tag) {
            $parts = explode(':', trim($tag));
            if (! empty($parts[0])) {
                $versions[$parts[0]] = $parts[1] ?? null;
            }
        }

        return match ($model) {
            'category' => Category::whereIn('name', array_keys($versions))->get(),
            'tag' => Tag::whereIn('name', array_keys($versions))->get(),
            default => [],
        };
    }
}
