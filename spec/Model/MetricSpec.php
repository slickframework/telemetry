<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Telemetry\Model;

use Psr\Log\LogLevel;
use Slick\Telemetry\Model\Metric;
use PhpSpec\ObjectBehavior;
use Slick\Telemetry\Trackable;

/**
 * MetricSpec specs
 *
 * @package spec\Slick\Telemetry\Model
 */
class MetricSpec extends ObjectBehavior
{

    private $message;
    private $value;
    private $count;
    private $min;
    private $max;
    private $stdDev;
    private $context;

    function let()
    {
        $this->message = 'CPU Usage';
        $this->value = 1.87;
        $this->count = 6;
        $this->min = 1.5;
        $this->max = 1.99;
        $this->stdDev = 0.24;
        $this->context = ['foo' => 'bar'];
        $this->beConstructedWith(
            $this->message,
            $this->value,
            $this->count,
            $this->min,
            $this->max,
            $this->stdDev,
            $this->context
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Metric::class);
    }

    function its_a_trackable()
    {
        $this->shouldBeAnInstanceOf(Trackable::class);
    }

    function it_has_a_message()
    {
        $this->message()->shouldBe($this->message);
    }

    function it_has_a_value()
    {
        $this->value()->shouldBe($this->value);
    }

    function it_has_a_count()
    {
        $this->count()->shouldBe($this->count);
    }

    function it_has_a_min()
    {
        $this->min()->shouldBe($this->min);
    }

    function it_has_a_max()
    {
        $this->max()->shouldBe($this->max);
    }

    function it_has_a_std_dev()
    {
        $this->stdDev()->shouldBe($this->stdDev);
    }

    function it_has_a_context()
    {
        $this->context()->shouldBe([
            'foo' => 'bar',
            'label' => Trackable::LABEL_METRIC,
            'value' => $this->value,
            'count' => $this->count,
            'min' => $this->min,
            'max' => $this->max,
            'stdDev' => $this->stdDev
        ]);
    }

    function it_has_a_log_level()
    {
        $this->logLevel()->shouldBe(LogLevel::INFO);
    }

    function it_has_a_label()
    {
        $this->label()->shouldBe(Trackable::LABEL_METRIC);
    }
}
