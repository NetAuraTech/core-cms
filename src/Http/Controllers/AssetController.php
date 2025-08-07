<?php

namespace Netauratech\CoreCms\Http\Controllers;

use Illuminate\Http\Response;
use Netauratech\CoreCms\Contracts\AssetSourceInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetController extends Controller
{
    /**
     * @var AssetSourceInterface[]
     */
    protected array $assetSources;

    /**
     * Controller constructor.
     *
     * @param array $assetSources An array of implementations of AssetSourceInterface.
     */
    public function __construct(array $assetSources)
    {
        $this->assetSources = $assetSources;
    }

    /**
     * Serves assets by trying different sources.
     *
     * @param string $path The path to the requested asset.
     * @return BinaryFileResponse|Response
     */
    public function show(string $path): BinaryFileResponse|Response
    {
        foreach ($this->assetSources as $source) {
            $response = $source->resolve($path);
            if ($response !== null) {
                return $response;
            }
        }

        abort(404);
    }
}
