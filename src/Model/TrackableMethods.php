<?php

/**
 * This file is part of Telemetry
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Telemetry\Model;

use Psr\Log\LogLevel;
use Slick\Telemetry\Trackable;

/**
 * TrackableMethods trait
 *
 * @package Slick\Telemetry\Model
 */
trait TrackableMethods
{
    protected string $message;
    protected iterable $context;
    protected string $logLevel = LogLevel::INFO;
    protected string $label = Trackable::LABEL_MISC;

    /**
     * A brief log message
     *
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * A list of key/value pairs defining trackable context
     *
     * @return iterable
     */
    public function context(): iterable
    {
        return $this->context;
    }

    /**
     * Returns a new object with provided context
     *
     * @param iterable $context
     * @return Trackable
     */
    public function withContext(iterable $context): Trackable
    {
        $clone = clone $this;
        $clone->context = $context;
        return $clone;
    }

    /**
     * Trackable log level
     *
     * @return string
     */
    public function logLevel(): string
    {
        return $this->logLevel;
    }

    /**
     * type
     *
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }
}
