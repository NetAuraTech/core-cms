<?php

namespace Netauratech\CoreCms\Contracts;

interface CommentableInterface
{
    /**
     * Get the unique ID of the commentable entity.
     * @return int|string
     */
    public function getId(): int|string;

    /**
     * Get the polymorphic type of the commentable entity.
     * Used for the “commentable_type” column.
     * @return string
     */
    public function getMorphClass(): string;
}