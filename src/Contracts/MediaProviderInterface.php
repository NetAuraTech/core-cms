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
     * Generates an HTML <img> tag to display an image.
     *
     * This function takes an image entity or path and constructs an <img> tag
     * with options for alternative text, height, CSS transitions,
     * additional classes, and preloading.
     *
     * @param int $id The image id.
     * @param string|null $alt The alternative text for the image, for accessibility. Defaults to null.
     * @param int|null $height The height of the image in pixels. Defaults to null.
     * @param string|null $transitionName A CSS transition name (e.g., for frontend animations). Defaults to null.
     * @param string|null $classes Additional CSS classes to apply to the <img> tag. Defaults to null.
     * @param array $styles Additional styles to apply to the <img> tag. Defaults to [].
     * @return string|null The generated HTML <img> tag as a string, or null if the image cannot be generated.
     */
    public function image_tag(int $id, ?string $alt = null, ?int $height = null, ?string $transitionName = null, ?string $classes = null, array $styles = []): ?string;

    /**
     * Retrieves a Media object by its ID.
     *
     * @param int $id The ID of the media.
     * @return object|null The Media object or null if not found.
     */
    public function get(int $id): ?object;
}