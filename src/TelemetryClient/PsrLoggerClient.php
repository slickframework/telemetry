<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Telemetry\TelemetryClient;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Slick\Telemetry\TelemetryClient;
use Slick\Telemetry\Trackable;

/**
 * PsrLoggerClient
 *
 * @package Slick\Telemetry\TelemetryClient
 */
final class PsrLoggerClient implements TelemetryClient
{
    use LoggerTrait;
    use TelemetryClientTrait;
    use RequestFactoryTrait;

    /**
     * Creates a PsrLoggerClient
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array())
    {
        $this->logger->log($level, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function track(Trackable $trackableData): TelemetryClient
    {
        $context = array_merge((array) $this->context, (array) $trackableData->context());
        $message = $this->interpolate($trackableData->message(), $context);
        $this->log($trackableData->logLevel(), $message, $context);
        return $this;
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    private function interpolate(string $message, iterable $context = []): string
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be cast to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}
