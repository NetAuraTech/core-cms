<?php

namespace Netauratech\CoreCms\Observers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\File;
use JsonException;
use Netauratech\CoreCms\Jobs\PrecacheContent;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Contracts\CacheServiceInterface;
use Netauratech\CoreCms\Contracts\PurgeUrlProviderInterface;

class ContentObserver
{
    /**
     * @throws ConnectionException
     * @throws JsonException
     */
    public function saving(Content $content): void
    {
        $path = storage_path("app/public/css");
        $css = '';
        foreach ($content->getContent() as $block) {
            if(!empty($block['layout-items'])) {
                foreach ($block['layout-items'] as $item) {
                    $css = $this->generate($item, $css);
                }
            }

            $css = $this->generate($block, $css);
        }

        File::ensureDirectoryExists($path);
        $filePath = "{$path}/{$content->slug}.css";
        File::put($filePath, $css);

        $this->purge($content);
    }

    static function generate(mixed $block, string $css): string
    {
        $hash = substr(md5(json_encode($block)), 0, 8);
        $baseClass = ".block__{$hash}";

        $groups = [];

        foreach ($block as $key => $value) {
            if (is_array($value) || is_null($value)) {
                continue;
            }

            if (str_contains($key, '_animation')) {
                continue;
            }

            $validSuffixes = [
                '-color',
                '-border-style',
                '-border-line',
                '-border-color',
                '-opacity',
                '-transition-name',
                '_delay',
                // Background
                '-image',
                '-image-opacity',
                '-image-size',
                '-image-repeat',
                '-image-position-x',
                '-image-position-y',
                // Layout/Root
                'min-item-size',
                'gap',
            ];

            $hasValidSuffix = false;
            foreach ($validSuffixes as $suffix) {
                if (str_ends_with($key, $suffix) || $key === $suffix) {
                    $hasValidSuffix = true;
                    break;
                }
            }

            if (!$hasValidSuffix) {
                continue;
            }

            $prefix = 'root';
            foreach ($validSuffixes as $suffix) {
                if (str_ends_with($key, $suffix)) {
                    $prefix = substr($key, 0, -strlen($suffix));

                    if($prefix == "") {
                        $prefix = 'root';
                    }

                    break;
                }
                if ($key === $suffix) {
                    $prefix = 'root';
                    break;
                }
            }

            $groups[$prefix][$key] = $value;
        }

        // --- Root block treatment ---
        $rootRules = "";

        if (isset($groups['background'])) {
            foreach ($groups['background'] as $key => $value) {
                if ($value === '' || $value === 'transparent') {
                    continue;
                }

                if ($key === 'background-image') {
                    $rootRules .= "--background-image:url(".image_url($value).");";
                } elseif (str_starts_with($key, 'background-image-')) {
                    $cssVar = str_replace('background-image-', 'background-image-', $key);
                    $rootRules .= "--{$cssVar}:{$value};";
                } elseif ($key === 'background-color') {
                    $rootRules .= "--background-color:{$value};";
                }
            }
        }

        if (isset($groups['general'])) {
            foreach ($groups['general'] as $key => $value) {
                if ($value === '' || $value === 'transparent') {
                    continue;
                }

                if (str_ends_with($key, '-transition-name')) {
                    $rootRules .= "view-transition-name:{$value};";
                }
            }
        }

        if (trim($rootRules) !== "") {
            $css .= "{$baseClass}{{$rootRules}}";
        }

        foreach ($groups as $prefix => $keys) {
            if ($prefix === 'background' || $prefix === 'general' || $prefix === 'root') {
                continue;
            }

            $rules = "";

            $hasBorderStyle = false;
            foreach ($keys as $key => $value) {
                if (str_ends_with($key, '-border-style') && $value !== '') {
                    $hasBorderStyle = true;
                    break;
                }
            }

            foreach ($keys as $key => $value) {
                if ($value === '' || $value === 'transparent') {
                    // Exception for opacity
                    if (!str_ends_with($key, '-opacity')) {
                        continue;
                    }
                }

                // color
                if (str_ends_with($key, '-color')) {
                    $rules .= "color:{$value};";
                }
                // border-style
                elseif (str_ends_with($key, '-border-style')) {
                    $rules .= "text-decoration-style:{$value};";
                    $rules .= "text-decoration-thickness:3px;";
                }
                // border-line
                elseif (str_ends_with($key, '-border-line') && $hasBorderStyle) {
                    $rules .= "text-decoration-line:{$value};";
                }
                // border-color
                elseif (str_ends_with($key, '-border-color') && $hasBorderStyle && $value !== 'transparent') {
                    $rules .= "text-decoration-color:{$value};";
                }
                // opacity
                elseif (str_ends_with($key, '-opacity')) {
                    $rules .= "opacity:{$value};";
                }
                // transition-name
                elseif (str_ends_with($key, '-transition-name')) {
                    $rules .= "view-transition-name:{$value};";
                }
            }

            if (trim($rules) !== "") {
                $elementClass = "{$baseClass}-{$prefix}";
                $css .= "{$elementClass}{{$rules}}";
            }
        }

        // --- Layout block ---
        if (isset($groups['root'])) {
            $layoutRules = "";

            foreach ($groups['root'] as $key => $value) {
                if ($value === '') {
                    continue;
                }

                if ($key === 'min-item-size') {
                    $layoutRules .= "--min-item-size:{$value}px;";
                } elseif ($key === 'gap') {
                    $layoutRules .= "--grid-gap:{$value}rem;";
                } elseif (str_starts_with($key, 'padding-') || str_starts_with($key, 'border-')) {
                    // Other root properties if necessary
                }
            }

            if (trim($layoutRules) !== "") {
                $layoutClass = "{$baseClass}-layout";
                $css .= "{$layoutClass}{{$layoutRules}}";
            }
        }

        if ($css == "") {
            $css = "/* dummy */";
        }

        return $css;
    }

    /**
     * @throws ConnectionException
     */
    public function purge(Content $content): void
    {
        $urlsToPurge = [];

        /** @var PurgeUrlProviderInterface[] $providers */
        $providers = app()->tagged('content_purge_providers');

        if (in_array($content->type, ['footer', 'header'])) {
            app(CacheServiceInterface::class)->clear();

            foreach ($providers as $provider) {
                if ($provider instanceof PurgeUrlProviderInterface) {
                    $urlsToPurge = array_merge($urlsToPurge, $provider->getAllManagedUrls());
                }
            }
        } else {
            foreach ($providers as $provider) {
                if ($provider instanceof PurgeUrlProviderInterface) {
                    $urlsToPurge = array_merge($urlsToPurge, $provider->getUrlsToPurge($content));
                }
            }
        }

        $urlsToPurge = array_unique($urlsToPurge);

        if (!empty($urlsToPurge)) {
            app(CacheServiceInterface::class)->purgeItems($urlsToPurge);
        }

        foreach ($urlsToPurge as $url) {
            PrecacheContent::dispatch(url($url));
        }
    }
}