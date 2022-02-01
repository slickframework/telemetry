<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Telemetry\Model;

use JsonSerializable;
use Slick\Event\Event as SlickEvent;
use Slick\Telemetry\Trackable;

/**
 * Event
 *
 * @package Slick\Telemetry\Model
 */
final class Event implements Trackable
{
    use TrackableMethods;

    /**
     * Creates a Event
     *
     * @param SlickEvent $event
     * @param iterable|null $context
     */
    public function __construct(SlickEvent $event, ?iterable $context = [])
    {
        $this->message = $this->parseEventName($event);
        $this->label = Trackable::LABEL_EVENT;
        $this->context = array_merge($context, [
            'label' => $this->label,
            'occurredOn' => $event->occurredOn()->format(\DateTime::W3C)
        ]);

        if ($event instanceof JsonSerializable) {
            $this->context['data'] = json_encode($event);
        }
    }

    /**
     * Parse event name from its class name
     *
     * @param SlickEvent $event
     * @return string
     */
    private function parseEventName(SlickEvent $event): string
    {
        $parts = explode("\\", get_class($event));
        $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', end($parts)));
        return ucfirst(str_replace('_', ' ', $name));
    }
}
