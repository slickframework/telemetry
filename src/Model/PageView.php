<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Telemetry\Model;

use Slick\Telemetry\Trackable;

/**
 * PageView
 *
 * @package Slick\Telemetry\Model
 */
final class PageView implements Trackable
{
    use TrackableMethods;

    private string $path;
    private float $duration;

    /**
     * Creates a PageView
     *
     * @param string $message
     * @param string $path
     * @param float $duration
     * @param iterable $context
     */
    public function __construct(string $message, string $path, float $duration = 0, iterable $context = [])
    {
        $this->message = $message;
        $this->path = $path;
        $this->duration = $duration;
        $this->label = Trackable::LABEL_PAGE_VIEW;
        $this->context = array_merge($context, [
            'label' => $this->label,
            'path' => $path,
            'duration' => $duration
        ]);
    }

    /**
     * path
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * duration
     *
     * @return int|null
     */
    public function duration(): float
    {
        return $this->duration;
    }
}
