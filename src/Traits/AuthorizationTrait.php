<?php
namespace Anexia\Monitoring\Traits;

/**
 * Trait AuthorizationTrait
 * @package Anexia\Monitoring\Traits
 */
trait AuthorizationTrait
{
    /**
     * Simple token based authorization check
     *
     * @return bool
     */
    public function checkAccessToken()
    {
        $token = config('monitoring.access_token');

        if (!$token) {
            // no valid access_token given in config file .env
            return false;
        }

        if (request()->get('access_token') !== $token) {
            // given token (GET parameter) does not match expected token (config)
            return false;
        }

        // token check successful
        return true;
    }
}