<?php

namespace Netauratech\CoreCms\Services;

use Netauratech\CoreCms\Contracts\MediaProviderInterface;

class NullMediaProvider implements MediaProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getImageUrl(string|int|object|null $entity, ?int $width = null, ?int $height = null): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getAttachmentById(string|int $id): ?object
    {
        return null;
    }
}