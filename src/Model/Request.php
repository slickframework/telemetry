<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Telemetry\Model;

use Psr\Log\LogLevel;
use Slick\Telemetry\Trackable;

/**
 * Request
 *
 * @package Slick\Telemetry\Model
 */
final class Request implements Trackable
{
    use TrackableMethods;

    private string $path;
    private int $startTime;
    private ?int $statusCode;
    private float $duration;

    /**
     * Creates a Request
     *
     * @param string $message
     * @param string $path
     * @param int $startTime
     * @param int $statusCode
     * @param float $duration
     * @param iterable|null $context
     */
    public function __construct(
        string $message,
        string $path,
        int $startTime,
        int $statusCode = 200,
        float $duration = 0,
        ?iterable $context = []
    ) {
        $this->message = $message;
        $this->path = $path;
        $this->startTime = $startTime;
        $this->statusCode = $statusCode;
        $this->label = Trackable::LABEL_REQUEST;
        $this->duration = $duration;
        $this->context = array_merge($context, [
            'label' => $this->label,
            'path' => $path,
            'startTime' => $startTime,
            'statusCode' => $statusCode,
            'duration' => $duration,
            'isSuccessful' => $this->isSuccessful()
        ]);

        $this->logLevel = $this->isSuccessful() ? $this->logLevel : LogLevel::WARNING;
    }

    /**
     * Requested path
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Request start timestamp
     *
     * @return int
     */
    public function startTime(): int
    {
        return $this->startTime;
    }

    /**
     * HTTP request status code
     *
     * @return int
     */
    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * States if the request is a successful one, or not
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode < 400;
    }

    /**
     * duration
     *
     * @return float|int
     */
    public function duration()
    {
        return $this->duration;
    }
}
