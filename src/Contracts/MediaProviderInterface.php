<?php

namespace Netauratech\CoreCms\Contracts;

interface MediaProviderInterface
{
    /**
     * Generates a URL for an image, potentially resized.
     *
     * @param string|int $id The Media ID.
     * @param int|null $width The desired width for the image.
     * @param int|null $height The desired height for the image.
     * @return string The URL of the image.
     */
    public function getImageUrl(string|int $id, ?int $width = null, ?int $height = null): string;

    /**
     * Retrieves a Media object by its ID.
     *
     * @param int $id The ID of the media.
     * @return object|null The Media object or null if not found.
     */
    public function get(int $id): ?object;
}