<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support;

use Proovit\LaravelProovit\Exceptions\ApiException;
use Proovit\LaravelProovit\ProovitClient;
use RuntimeException;
use Throwable;

final class ProovitApiSessionGuard
{
    /**
     * @template T
     *
     * @param  callable():T  $callback
     * @return T
     */
    public function withAutoRefresh(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (ApiException $exception) {
            if (! $this->shouldRefresh($exception)) {
                throw $exception;
            }

            $this->refreshBearer();

            return $callback();
        }
    }

    private function shouldRefresh(ApiException $exception): bool
    {
        return in_array($exception->statusCode, [401, 403], true);
    }

    private function refreshBearer(): void
    {
        $client = app(ProovitClient::class);

        try {
            $client->connection()->refreshBearer();
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'The ProovIT bearer token could not be refreshed automatically. Please reconnect the settings page.',
                previous: $exception
            );
        }
    }
}
