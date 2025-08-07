<?php

namespace Netauratech\CoreCms\Contracts;

interface MediaProviderInterface
{
    /**
     * Generates a URL for an image, potentially resized.
     *
     * @param string|int|object|null $entity The attachment ID, file name, or the Attachment object itself.
     * @param int|null $width The desired width for the image.
     * @param int|null $height The desired height for the image.
     * @return string The URL of the image.
     */
    public function getImageUrl(string|int|object|null $entity, ?int $width = null, ?int $height = null): string;

    /**
     * Retrieves an Attachment object by its ID.
     *
     * @param string|int $id The ID of the attachment.
     * @return object|null The Attachment object or null if not found.
     */
    public function getAttachmentById(string|int $id): ?object;
}