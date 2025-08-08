<?php

namespace Netauratech\CoreCms\Contracts;

use Illuminate\Http\Response;

interface ChallengeGeneratorInterface
{
    public function generate(string $key): Response;
}
