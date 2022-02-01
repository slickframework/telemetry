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
 * Metric
 *
 * @package Slick\Telemetry\Model
 */
final class Metric implements Trackable
{
    use TrackableMethods;

    private float $value;
    private ?int $count;
    private ?float $min;
    private ?float $max;
    private ?float $stdDev;

    /**
     * Creates a Metric
     *
     * @param string $message
     * @param float $value
     * @param int|null $count
     * @param float|null $min
     * @param float|null $max
     * @param float|null $stdDev
     * @param iterable|null $context
     */
    public function __construct(
        string $message,
        float $value,
        ?int $count = null,
        ?float $min = null,
        ?float $max = null,
        ?float $stdDev = null,
        ?iterable $context = []
    ) {
        $this->message = $message;
        $this->value = $value;
        $this->count = $count;
        $this->min = $min;
        $this->max = $max;
        $this->stdDev = $stdDev;
        $this->label = Trackable::LABEL_METRIC;
        $this->context = array_merge(
            $context,
            ['label' => $this->label],
            compact('value', 'count', 'min', 'max', 'stdDev')
        );
    }

    /**
     * value
     *
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * count
     *
     * @return int|null
     */
    public function count(): ?int
    {
        return $this->count;
    }

    /**
     * min
     *
     * @return float|null
     */
    public function min(): ?float
    {
        return $this->min;
    }

    /**
     * max
     *
     * @return float|null
     */
    public function max(): ?float
    {
        return $this->max;
    }

    /**
     * stdDev
     *
     * @return float|null
     */
    public function stdDev(): ?float
    {
        return $this->stdDev;
    }
}
