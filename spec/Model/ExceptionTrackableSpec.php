<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Telemetry\Model;

use Psr\Log\LogLevel;
use Slick\Telemetry\Model\ExceptionTrackable;
use PhpSpec\ObjectBehavior;
use Slick\Telemetry\Trackable;

/**
 * ExceptionTrackableSpec specs
 *
 * @package spec\Slick\Telemetry\Model
 */
class ExceptionTrackableSpec extends ObjectBehavior
{

    private $message;
    private ?\Exception $exception;

    function let()
    {
        $this->message = 'Some error';
        $this->exception = new \Exception($this->message);

        $this->beConstructedWith($this->exception, ['foo' => 'bar']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ExceptionTrackable::class);
    }

    function its_a_trackable()
    {
        $this->shouldBeAnInstanceOf(Trackable::class);
    }

    function it_has_a_message()
    {
        $this->message()->shouldBe($this->message);
    }

    function it_has_a_exception()
    {
        $this->exception()->shouldBe($this->exception);
    }

    function it_has_a_level()
    {
        $this->logLevel()->shouldBe(LogLevel::ALERT);
    }

    function it_has_a_context()
    {
        $this->context()->shouldBe([
            'foo' => 'bar',
            'label' => Trackable::LABEL_EXCEPTION,
            'exception' => get_class($this->exception),
            'file' => "{$this->exception->getFile()} ({$this->exception->getLine()})",
            'trace' => $this->exception->getTraceAsString()
        ]);
    }

    function it_has_a_label()
    {
        $this->label()->shouldBe(Trackable::LABEL_EXCEPTION);
    }
}
