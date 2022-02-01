<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Telemetry\Model;

use Psr\Log\LogLevel;
use Slick\Telemetry\Model\PageView;
use PhpSpec\ObjectBehavior;
use Slick\Telemetry\Trackable;

/**
 * PageViewSpec specs
 *
 * @package spec\Slick\Telemetry\Model
 */
class PageViewSpec extends ObjectBehavior
{

    private $message;
    private $path;
    private $duration;
    private $context;

    function let()
    {
        $this->message = 'Home page request';
        $this->path = '/';
        $this->duration = 9283776.02;
        $this->context = ['foo' => 'bar'];
        $this->beConstructedWith($this->message, $this->path, $this->duration, $this->context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PageView::class);
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

    function it_has_a_duration()
    {
        $this->duration()->shouldBe($this->duration);
    }

    function it_has_a_label()
    {
        $this->label()->shouldBe(Trackable::LABEL_PAGE_VIEW);
    }

    function it_has_a_context()
    {
        $context = $this->context();
        $context->shouldBeArray();
        $context->shouldHaveCount(4);
        $context->shouldBe([
            'foo' => 'bar',
            'label' => Trackable::LABEL_PAGE_VIEW,
            'path' => $this->path,
            'duration' => $this->duration
        ]);
    }

    function it_has_a_log_level()
    {
        $this->logLevel()->shouldBe(LogLevel::INFO);
    }
}
