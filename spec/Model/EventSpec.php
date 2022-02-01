<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Telemetry\Model;

use Slick\Event\Domain\AbstractEvent;
use Slick\Telemetry\Model\Event;
use Slick\Event\Event as SlickEvent;
use PhpSpec\ObjectBehavior;
use Slick\Telemetry\Trackable;

/**
 * EventSpec specs
 *
 * @package spec\Slick\Telemetry\Model
 */
class EventSpec extends ObjectBehavior
{

    /**
     * @var EventWasUsed
     */
    private $event;

    function let()
    {
        $this->event = new EventWasUsed();
        $this->beConstructedWith($this->event, ['foo' => 'bar']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Event::class);
    }

    function its_a_trackable()
    {
        $this->beAnInstanceOf(Trackable::class);
    }

    function it_has_a_message()
    {
        $this->message()->shouldBe('Event was used');
    }

    function it_has_a_context()
    {
        $this->context()->shouldBe([
            'foo' => 'bar',
            'label' => Trackable::LABEL_EVENT,
            'occurredOn' => $this->event->occurredOn()->format(\DateTime::W3C),
            'data' => json_encode($this->event)
        ]);
    }

    function it_only_has_data_when_event_can_be_serialized_to_json(SlickEvent $event)
    {
        $dateTimeImmutable = new \DateTimeImmutable();
        $event->occurredOn()->willReturn($dateTimeImmutable);
        $this->beConstructedWith($event);
        $this->context()->shouldBe([
            'label' => Trackable::LABEL_EVENT,
            'occurredOn' => $dateTimeImmutable->format(\DateTime::W3C)
        ]);
    }

    function it_has_a_label()
    {
        $this->label()->shouldBe(Trackable::LABEL_EVENT);
    }
}

class EventWasUsed extends AbstractEvent implements SlickEvent, \JsonSerializable
{
    public function __construct()
    {
        parent::__construct();
    }

    public function jsonSerialize()
    {
        return 'it runs';
    }
}