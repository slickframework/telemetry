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
 * Dependency
 *
 * @package Slick\Telemetry\Model
 */
final class Dependency implements Trackable
{
    use TrackableMethods;

    private string $type;
    private ?string $command;
    private ?int $startTime;
    /**
     * @var float|int|null
     */
    private $duration;
    private ?bool $successful;

    /**
     * Creates a Dependency
     *
     * @param string $message
     * @param string $type
     * @param string|null $command
     * @param int|null $startTime
     * @param float|null $duration
     * @param bool|null $successful
     * @param iterable|null $context
     */
    public function __construct(
        string $message,
        string $type,
        ?string $command = null,
        ?int $startTime = null,
        float $duration = 0,
        bool $successful = true,
        iterable $context = []
    ) {
        $this->message = $message;
        $this->type = $type ?: time();
        $this->command = $command;
        $this->startTime = $startTime;
        $this->duration = $duration;
        $this->successful = $successful;
        $this->label = Trackable::LABEL_DEPENDENCY;
        $this->context = array_merge($context, [
            'label' => Trackable::LABEL_DEPENDENCY,
            'type' => $type,
            'command' => $command,
            'startTime' => $startTime,
            'duration' => $duration,
            'isSuccessful' => $successful
        ]);
        if (!$successful) {
            $this->logLevel = LogLevel::WARNING;
        }
    }

    /**
     * type
     *
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * command
     *
     * @return string|null
     */
    public function command(): ?string
    {
        return $this->command;
    }

    /**
     * Command start timestamp
     *
     * @return int
     */
    public function startTime(): int
    {
        return $this->startTime;
    }

    /**
     * Command/dependency execution duration
     *
     * @return float
     */
    public function duration(): float
    {
        return $this->duration;
    }

    /**
     * successful
     *
     * @return bool
     */
    public function isSuccessful(): ?bool
    {
        return $this->successful;
    }
}
