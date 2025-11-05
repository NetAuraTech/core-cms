<?php

namespace Netauratech\CoreCms\Services;

use Illuminate\Database\Eloquent\Model;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;
use Netauratech\CoreCms\Contracts\PurgeUrlProviderInterface;
use Netauratech\CoreCms\Models\Option;

class ContentPurgeProvider implements PurgeUrlProviderInterface
{
    public ContentProviderInterface $contentProvider;
    public function __construct(ContentProviderInterface $contentProvider)
    {
        $this->contentProvider = $contentProvider;
    }

    /**
     * @inheritDoc
     */
    public function getUrlsToPurge(Model $content): array
    {
        $urls = [];

        if ($content->type === 'page') {
            $urls[] = "/{$content->slug}";
        }

        $homepageOption = Option::find('homepage');
        if ($homepageOption && (string)$content->id === $homepageOption->value) {
            $urls[] = "/";
        }

        return $urls;
    }

    /**
     * @inheritDoc
     */
    public function getAllManagedUrls(): array
    {
        $urls = [];

        $contents = $this->contentProvider->getContents("page");
        foreach ($contents as $content) {
            $urls[] = "/{$content->slug}";
        }

        $urls[] = "/";

        return $urls;
    }
}