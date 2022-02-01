<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Telemetry\Model;

use Psr\Log\LogLevel;
use Slick\Telemetry\Model\Dependency;
use PhpSpec\ObjectBehavior;
use Slick\Telemetry\Trackable;

/**
 * DependencySpec specs
 *
 * @package spec\Slick\Telemetry\Model
 */
class DependencySpec extends ObjectBehavior
{
    private $message;
    private $type;
    private $command;
    private $startTime;
    private $duration;
    private $successful;
    private $context;

    function let()
    {
        $this->message = 'Query';
        $this->type = 'MySQL';
        $this->command = 'SELECT * FROM test';
        $this->startTime = time();
        $this->duration = 1231.87;
        $this->successful = true;
        $this->context = ['server' => 'localhost'];
        $this->beConstructedWith(
            $this->message,
            $this->type,
            $this->command,
            $this->startTime,
            $this->duration,
            $this->successful,
            $this->context
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Dependency::class);
    }

    function its_a_trackable()
    {
        $this->shouldBeAnInstanceOf(Trackable::class);
    }

    function it_has_a_message()
    {
        $this->message()->shouldBe($this->message);
    }

    function it_has_a_type()
    {
        $this->type()->shouldBe($this->type);
    }

    function it_has_a_command()
    {
        $this->command()->shouldBe($this->command);
    }

    function it_has_a_start_time()
    {
        $this->startTime()->shouldBe($this->startTime);
    }

    function it_has_a_duration()
    {
        $this->duration()->shouldBe($this->duration);
    }

    function it_has_a_is_successful_tag()
    {
        $this->isSuccessful()->shouldBe($this->successful);
    }

    function it_has_a_context()
    {
        $this->context()->shouldBe([
            'server' => 'localhost',
            'label' => Trackable::LABEL_DEPENDENCY,
            'type' => $this->type,
            'command' => $this->command,
            'startTime' => $this->startTime,
            'duration' => $this->duration,
            'isSuccessful' => $this->successful
        ]);
    }

    function it_has_a_warning_log_level_when_not_successful()
    {
        $this->beConstructedWith(
            $this->message,
            $this->type,
            $this->command,
            $this->startTime,
            $this->duration,
            false
        );
        $this->logLevel()->shouldBe(LogLevel::WARNING);
    }

    function it_has_a_label()
    {
        $this->label()->shouldBe(Trackable::LABEL_DEPENDENCY);
    }
}
