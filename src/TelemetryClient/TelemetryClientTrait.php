<?php

/**
 * This file is part of Telemetry
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Telemetry\TelemetryClient;

use Psr\Log\LoggerInterface;
use Slick\Event\Event;
use Slick\Telemetry\Model\Dependency;
use Slick\Telemetry\Model\Event as TrackabkeEvent;
use Slick\Telemetry\Model\ExceptionTrackable;
use Slick\Telemetry\Model\Metric;
use Slick\Telemetry\Model\PageView;
use Slick\Telemetry\Model\Request;
use Slick\Telemetry\TelemetryClient;
use Slick\Telemetry\Trackable;
use Throwable;

/**
 * TelemetryClientTrait trait
 *
 * @package Slick\Telemetry\TelemetryClient
 */
trait TelemetryClientTrait
{
    protected iterable $context = [];
    protected LoggerInterface $logger;

    /**
     * @inheritDoc
     */
    public function context(): iterable
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public function withContextParam(string $param, $value): iterable
    {
        $this->context[$param] = $value;
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public function withoutContextParam(string $param): iterable
    {
        if (array_key_exists($param, $this->context)) {
            unset($this->context[$param]);
        }
        return $this->context;
    }

    abstract public function track(Trackable $trackableData): TelemetryClient;

    /**
     * @inheritDoc
     */
    public function trackPageView(
        string $message,
        string $path,
        float $duration = 0,
        iterable $context = []
    ): TelemetryClient {
        return $this->track(new PageView($message, $path, $duration, $context));
    }

    /**
     * @inheritDoc
     */
    public function trackMetric(
        string $message,
        float $value,
        ?int $count = null,
        ?float $min = null,
        ?float $max = null,
        ?float $stdDev = null,
        iterable $context = []
    ): TelemetryClient {
        $this->track(
            new Metric($message, $value, $count, $min, $max, $stdDev)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function trackEvent(Event $event, iterable $context = []): TelemetryClient
    {
        $this->track(new TrackabkeEvent($event, $context));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function trackRequest(
        string $message,
        string $path,
        int $startTime,
        int $statusCode = 200,
        float $duration = 0,
        iterable $context = []
    ): TelemetryClient {
        $this->track(
            new Request($message, $path, $startTime, $statusCode, $duration, $context)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function trackDependency(
        string $message,
        string $type,
        ?string $command = null,
        ?int $startTime = null,
        float $duration = 0,
        bool $successful = true,
        iterable $context = []
    ): TelemetryClient {
        $this->track(new Dependency($message, $type, $command, $startTime, $duration, $successful, $context));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function trackException(Throwable $exception, iterable $context = []): TelemetryClient
    {
        $this->track(new ExceptionTrackable($exception, $context));
        return $this;
    }
}
