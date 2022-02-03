<?php

/**
 * This file is part of Telemetry
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Telemetry\TelemetryClient;

use Slick\Telemetry\Model\Request;
use Slick\Telemetry\TelemetryClient;
use Slick\Telemetry\Trackable;

/**
 * RequestFactoryTrait trait
 *
 * @package Slick\Telemetry\TelemetryClient
 */
trait RequestFactoryTrait
{
    abstract public function track(Trackable $trackableData): TelemetryClient;

    /**
     * @inheritDoc
     */
    public function beginRequest(string $message, string $path, iterable $context = []): Request
    {
        $context = array_merge($context, ['start' => microtime(true)]);
        return new Request($message, $path, time(), 200, 0, $context);
    }

    /**
     * @inheritDoc
     */
    public function endRequest(Request $request, int $statusCode = 200, iterable $context = []): TelemetryClient
    {
        $start = $request->context()['start'] ?? (float)($request->startTime() * 1000);

        $duration = ((float) microtime(true) - $start) * 1000;
        $context = array_merge((array) $request->context(), $context);

        if (array_key_exists('start', $context)) {
            unset($context['start']);
        }

        $this->trackRequest(
            $request->message(),
            $request->path(),
            $request->startTime(),
            $statusCode,
            $duration,
            $context
        );
        return $this;
    }
}
