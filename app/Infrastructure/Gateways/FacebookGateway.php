<?php

namespace App\Infrastructure\Gateways;

interface FacebookGateway
{
    /**
     * Publish a post to the Facebook page feed.
     *
     * @param string $content The text content of the post
     * @return array ['success' => bool, 'post_id' => ?string, 'error' => ?string]
     */
    public function publishPost(string $content): array;
}
