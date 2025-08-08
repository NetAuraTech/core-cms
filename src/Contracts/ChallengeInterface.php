<?php

namespace Netauratech\CoreCms\Contracts;

interface ChallengeInterface
{
    public function generateKey(): string;

    public function getSolution(string $key): mixed;

    public function verify(string $key, string $answer): bool;

    public function check(string $key, string $answer): bool;
}
