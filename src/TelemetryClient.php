<?php

/**
 * This file is part of Telemetry
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Telemetry;

use Psr\Log\LoggerInterface;
use Slick\Event\Event;
use Slick\Telemetry\Model\Request;
use Throwable;

/**
 * TelemetryClient
 *
 * @package Slick\Telemetry
 */
interface TelemetryClient extends LoggerInterface
{

    /**
     * Global context
     *
     * @return iterable
     */
    public function context(): iterable;

    /**
     * Sets global context with provided parameter
     *
     * @param string $param
     * @param mixed $value
     * @return iterable
     */
    public function withContextParam(string $param, $value): iterable;

    /**
     * Removes provided parameter from global context
     *
     * @param string $param
     * @return iterable
     */
    public function withoutContextParam(string $param): iterable;

    /**
     * Tracks the provided data trackable object
     *
     * @param Trackable $trackableData
     * @return TelemetryClient
     */
    public function track(Trackable $trackableData): TelemetryClient;

    /**
     * Tracks provided data as a PageView trackable object
     *
     * @param string $message
     * @param string $path
     * @param int|null $duration
     * @param iterable|null $context
     * @return TelemetryClient
     */
    public function trackPageView(
        string $message,
        string $path,
        float $duration = 0,
        iterable $context = []
    ): TelemetryClient;

    /**
     * Tracks provided data as a Metric trackable object
     *
     * @param string $message
     * @param float $value
     * @param int|null $count
     * @param float|null $min
     * @param float|null $max
     * @param float|null $stdDev
     * @param iterable|null $context
     * @return TelemetryClient
     */
    public function trackMetric(
        string $message,
        float $value,
        ?int $count = null,
        ?float $min = null,
        ?float $max = null,
        ?float $stdDev = null,
        iterable $context = []
    ): TelemetryClient;

    /**
     * Tracks provided data as an Event trackable object
     *
     * @param Event $event
     * @param iterable|null $context
     * @return TelemetryClient
     */
    public function trackEvent(Event $event, iterable $context = []): TelemetryClient;

    /**
     * Tracks provided data as a Request trackable object
     *
     * @param string $message
     * @param string $path
     * @param int $startTime
     * @param int $statusCode
     * @param float $duration
     * @param iterable|null $context
     * @return TelemetryClient
     */
    public function trackRequest(
        string $message,
        string $path,
        int $startTime,
        int $statusCode = 200,
        float $duration = 0,
        iterable $context = []
    ): TelemetryClient;

    /**
     * Tracks provided data as a Dependency trackable object
     *
     * @param string $message
     * @param string $type
     * @param string|null $command
     * @param int|null $startTime
     * @param float $duration
     * @param bool $successful
     * @param iterable $context
     * @return TelemetryClient
     */
    public function trackDependency(
        string $message,
        string $type,
        ?string $command = null,
        ?int $startTime = null,
        float $duration = 0,
        bool $successful = true,
        iterable $context = []
    ): TelemetryClient;

    /**
     * Tracks provided data as an Exception trackable object
     *
     * @param Throwable $exception
     * @param iterable $context
     * @return TelemetryClient
     */
    public function trackException(Throwable $exception, iterable $context = []): TelemetryClient;

    /**
     * Begins a request trackable object
     *
     * @param string $message
     * @param string $path
     * @param iterable $context
     * @return Request
     */
    public function beginRequest(string $message, string $path, iterable $context = []): Request;

    /**
     * Finishes and updates a request trackable object
     *
     * @param Request $request
     * @param int $statusCode
     * @param iterable $context
     * @return TelemetryClient
     */
    public function endRequest(Request $request, int $statusCode = 200, iterable $context = []): TelemetryClient;
}
