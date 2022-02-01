<?php

/**
 * This file is part of slick/telemetry
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Telemetry;

/**
 * Trackable
 *
 * @package Slick\Telemetry
 */
interface Trackable
{
    /**#@+
     * @var string
     */
    public const LABEL_DEPENDENCY = 'Dependency';
    public const LABEL_EVENT      = 'Event';
    public const LABEL_EXCEPTION  = 'Exception';
    public const LABEL_METRIC     = 'Metric';
    public const LABEL_MISC       = 'Misc';
    public const LABEL_PAGE_VIEW  = 'Page View';
    public const LABEL_REQUEST    = 'Request';
    /**#@-*/

    /**
     * A brief log message
     *
     * @return string
     */
    public function message(): string;

    /**
     * A list of key/value pairs defining trackable context
     *
     * @return iterable
     */
    public function context(): iterable;

    /**
     * Returns a new object with provided context
     *
     * @param iterable $context
     * @return Trackable
     */
    public function withContext(iterable $context): Trackable;

    /**
     * Trackable log level
     *
     * @return string
     */
    public function logLevel(): string;

    /**
     * Current trackable type
     *
     * You can use a list of Trackable::LABEL_* constants or define your own
     *
     * @return string
     */
    public function label(): string;
}
