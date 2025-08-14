<?php

namespace Netauratech\CoreCms\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Support\Facades\Storage;
use Netauratech\CoreCms\Contracts\AssetSourceInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageAssetSource implements AssetSourceInterface
{
    public function resolve(string $path, ?string $theme): Response|BinaryFileResponse|null
    {
        try {
            $basePath = storage_path('app/public');
            $assetPath = "{$basePath}/{$path}";

            if (!File::exists($assetPath)) {
                abort(404, __('core-cms::core.asset.notfound'));
            }

            $extension = File::extension($assetPath);

            $mimeType = match ($extension) {
                'css' => 'text/css',
                'js' => 'application/javascript',
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                default => 'text/plain',
            };

            return FacadeResponse::make(File::get($assetPath), 200, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000',
            ]);
        } catch (\Exception $e) {
            Log::warning("StorageAssetSource: Error resolving storage asset for path {$path} : " . $e->getMessage());
        }

        return null;
    }
}