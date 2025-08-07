<?php

namespace Netauratech\CoreCms\Contracts;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Response;

interface AssetSourceInterface
{
    /**
     * Attempts to resolve a given asset path and return a binary file response.
     * Returns null if the asset is not found by this source.
     *
     * @param string $path The relative path of the asset (e.g., ‘images/logo.png’).
     * @return BinaryFileResponse|Response|null A binary file response if the asset is found, otherwise null.
     */
    public function resolve(string $path): BinaryFileResponse|Response|null;
}