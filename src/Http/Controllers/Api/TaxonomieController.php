<?php

namespace Netauratech\CoreCms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Netauratech\CoreCms\Models\Category;
use Netauratech\CoreCms\Models\Tag;
use Netauratech\CoreCms\Http\Controllers\AdminController;

class TaxonomieController extends AdminController
{
    protected array $permissions = [
        'content-create' => ['search'],
        'content-edit'   => ['search'],
    ];

    /**
     * Searches for categories or tags based on a query string.
     *
     * This method accepts a search query (`q`) and a `type` parameter
     * ('tag' or 'category') to determine which content type to search.
     * It performs a case-insensitive `LIKE` search on the name field
     * and orders results by the length of the name, returning a JSON response
     * containing the name and slug of the matched items.
     *
     * @param Request $request The incoming HTTP request. Expected to contain 'q' (search query) in its query parameters.
     * @param string $type The type of content to search ('tag' or 'category').
     * @return JsonResponse A JSON response containing an array of matched tags or categories,
     * each with 'name' and 'slug'. Returns an empty array if no search query is provided
     * or if the type is invalid.
     */
    public function search(Request $request, string $type): JsonResponse
    {
        $search = $request->query->get('q');
        if (null === $search) {
            return response()->json([]);
        }

        $values = match ($type) {
            'tag' => Tag::whereRaw('LOWER(tags.name) like "' . strtolower((string)$search) . '%"')->orderByRaw('LENGTH(tags.name) asc')->get()->toArray(),
            'category' => Category::whereRaw('LOWER(categories.name) like "' . strtolower((string)$search) . '%"')->orderByRaw('LENGTH(categories.name) asc')->get()->toArray(),
            default => [],
        };

        return response()->json(array_map(fn ($t) => [
            'name' => $t['name'],
            'slug' => $t['slug'],
        ], $values));
    }
}
