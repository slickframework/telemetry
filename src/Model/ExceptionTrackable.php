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
use Throwable;

/**
 * ExceptionTrackable
 *
 * @package Slick\Telemetry\Model
 */
final class ExceptionTrackable implements Trackable
{
    use TrackableMethods;

    private Throwable $exception;

    /**
     * Creates a ExceptionTrackable
     *
     * @param Throwable $exception
     * @param iterable $context
     */
    public function __construct(Throwable $exception, iterable $context = [])
    {
        $this->message = $exception->getMessage();
        $this->exception = $exception;
        $this->context = $context;
        $this->logLevel = LogLevel::ALERT;
        $this->label = Trackable::LABEL_EXCEPTION;
        $this->context = array_merge($context, [
            'label' => $this->label,
            'exception' => get_class($this->exception),
            'file' => "{$exception->getFile()} ({$exception->getLine()})",
            'trace' => $this->exception->getTraceAsString()
        ]);
    }

    /**
     * exception
     *
     * @return Throwable
     */
    public function exception(): Throwable
    {
        return $this->exception;
    }
}
