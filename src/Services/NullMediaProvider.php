<?php

namespace Netauratech\CoreCms\Services;

use Netauratech\CoreCms\Contracts\MediaProviderInterface;

class NullMediaProvider implements MediaProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getImageUrl(string|int $id, ?int $width = null, ?int $height = null): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function get(int $id): ?object
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function image_tag(int $id, ?string $alt = null, ?int $height = null, ?string $transitionName = null, ?string $class = null): ?string
    {
        return null;
    }
}