<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Telemetry\Model;

use Psr\Log\LogLevel;
use Slick\Telemetry\Model\Request;
use PhpSpec\ObjectBehavior;
use Slick\Telemetry\Trackable;

/**
 * RequestSpec specs
 *
 * @package spec\Slick\Telemetry\Model
 */
class RequestSpec extends ObjectBehavior
{
    private $message;
    private $path;
    private $startTime;
    private $statusCode;
    private $context;
    private $duration;

    function let()
    {
        $this->message = 'Update user';
        $this->path = '/user/43';
        $this->startTime = time();
        $this->statusCode = 200;
        $this->context = ['method' => 'GET'];
        $this->duration = 893.90;
        $this->beConstructedWith(
            $this->message,
            $this->path,
            $this->startTime,
            $this->statusCode,
            $this->duration,
            $this->context
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Request::class);
    }

    function its_a_trackable()
    {
        $this->shouldBeAnInstanceOf(Trackable::class);
    }

    function it_has_a_message()
    {
        $this->message()->shouldBe($this->message);
    }

    function it_has_a_path()
    {
        $this->path()->shouldBe($this->path);
    }

    function it_has_a_start_time()
    {
        $this->startTime()->shouldBe($this->startTime);
    }

    function it_has_a_status_code()
    {
        $this->statusCode()->shouldBe($this->statusCode);
    }

    function it_has_a_is_successful_flag()
    {
        $this->isSuccessful()->shouldBe(true);
    }

    function its_unsuccessful_when_status_code_is_not_2xx()
    {
        $this->beConstructedWith($this->message, $this->path, $this->startTime, 404);
        $this->isSuccessful()->shouldBe(false);
        $this->logLevel()->shouldBe(LogLevel::WARNING);
    }

    function it_has_a_label()
    {
        $this->label()->shouldBe(Trackable::LABEL_REQUEST);
    }

    function it_has_a_duration()
    {
        $this->duration()->shouldBe($this->duration);
    }

    function it_has_a_context()
    {
        $this->context()->shouldBe([
            'method' => 'GET',
            'label' => Trackable::LABEL_REQUEST,
            'path' => $this->path,
            'startTime' => $this->startTime,
            'statusCode' => $this->statusCode,
            'duration' => $this->duration,
            'isSuccessful' => true
        ]);
    }
}
