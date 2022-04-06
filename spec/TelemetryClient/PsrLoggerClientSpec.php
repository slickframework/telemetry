<?php

/**
 * This file is part of slick/telemetry package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Telemetry\TelemetryClient;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PhpSpec\Exception\Example\FailureException;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Slick\Event\Domain\AbstractEvent;
use Slick\Event\Event as SlickEvent;
use Slick\Telemetry\Model\Request;
use Slick\Telemetry\TelemetryClient;
use Slick\Telemetry\TelemetryClient\PsrLoggerClient;
use PhpSpec\ObjectBehavior;
use Slick\Telemetry\Trackable;

/**
 * PsrLoggerClientSpec specs
 *
 * @package spec\Slick\Telemetry\TelemetryClient
 */
class PsrLoggerClientSpec extends ObjectBehavior
{

    function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PsrLoggerClient::class);
    }

    function its_a_telemetry_client()
    {
        $this->shouldBeAnInstanceOf(TelemetryClient::class);
    }

    function it_has_a_context()
    {
        $this->context()->shouldBeArray();
        $this->context()->shouldHaveCount(0);
    }

    function it_can_set_a_context_value()
    {
        $this->withContextParam('foo', 'bar')->shouldBeArray();
        $this->context()['foo']->shouldBe('bar');
    }

    function it_can_remove_a_context_value()
    {
        $this->withContextParam('foo', 'bar')->shouldBeArray();
        $this->context()['foo']->shouldBe('bar');
        $this->withoutContextParam('foo')->shouldBeArray();
        $this->context()->shouldHaveCount(0);
    }

    function it_can_generate_and_track_a_page_view(LoggerInterface $logger)
    {
        $message = 'User register';
        $path = '/users';
        $duration = 0.34;
        $this->trackPageView($message, $path, $duration);
        $logger->log(
            LogLevel::INFO,
            $message,
            array_merge(
                ['label' => Trackable::LABEL_PAGE_VIEW],
                compact('path', 'duration')
            )
        )->shouldHaveBeenCalled();
    }

    function it_can_register_an_http_request(LoggerInterface $logger)
    {
        $message = 'read user';
        $path = '/user/34';
        $startTime = time();
        $statusCode = 204;
        $duration = 23.342;
        $this->trackRequest($message, $path, $startTime, $statusCode, $duration)->shouldBe($this->getWrappedObject());
        $logger->log(
            LogLevel::INFO,
            $message,
            array_merge(
                ['label' => Trackable::LABEL_REQUEST],
                compact('path', 'startTime', 'statusCode', 'duration'),
                ['isSuccessful' => true]
            )
        )->shouldHaveBeenCalled();
    }

    function it_can_register_a_metric(LoggerInterface $logger)
    {
        $message = 'Memory';
        $value = 234632;
        $count = 10;
        $min = 234600;
        $max = 234690;
        $stdDev = 34;
        $this->trackMetric($message, $value, $count, $min, $max, $stdDev);
        $logger->log(
            LogLevel::INFO,
            $message,
            array_merge(
                ['label' => Trackable::LABEL_METRIC],
                compact('value', 'count', 'min', 'max', 'stdDev')
            )
        )->shouldHaveBeenCalled();
    }

    function it_can_register_an_exception(LoggerInterface $logger)
    {
        $message = 'test';
        $exception = new \Exception($message);
        $this->trackException($exception);
        $logger->log(
            LogLevel::ALERT,
            $message,
            [
                'label' => Trackable::LABEL_EXCEPTION,
                'exception' => get_class($exception),
                'file' => "{$exception->getFile()} ({$exception->getLine()})",
                'trace' => $exception->getTraceAsString()
            ]
        )->shouldHaveBeenCalled();
    }

    function it_can_register_an_event(LoggerInterface $logger)
    {
        $event = new EventWasUsed();
        $this->trackEvent($event)->shouldBe($this->getWrappedObject());
        $logger->log(
            LogLevel::INFO,
            'Event was used',
            [
                'label' => Trackable::LABEL_EVENT,
                'occurredOn' => $event->occurredOn()->format(\DateTime::W3C),
                'data' => json_encode($event)
            ]
        )->shouldHaveBeenCalled();
    }

    function it_can_register_a_dependency(LoggerInterface $logger)
    {
        $message = 'MySQL';
        $type = 'Relational Database';
        $command = 'SELECT * FROM test';
        $startTime = time();
        $duration = 512.32;
        $isSuccessful = true;
        $this
            ->trackDependency($message, $type, $command, $startTime, $duration, $isSuccessful)
            ->shouldBe($this->getWrappedObject());

        $logger->log(
            LogLevel::INFO,
            $message,
            array_merge(
                ['label' => Trackable::LABEL_DEPENDENCY],
                compact('type', 'command', 'startTime', 'duration', 'isSuccessful')
            )
        )->shouldHaveBeenCalled();
    }

    function it_can_begin_a_new_request_trackable()
    {
        $this->beginRequest('Get users list', '/users')->shouldBeAnInstanceOf(Request::class);
    }

    function it_can_finish_and_track_a_request(LoggerInterface $logger)
    {
        $message = 'Get users list';
        $path = '/users';
        $request = $this->beginRequest($message, $path, ['method' => 'GET']);
        $request->shouldBeAnInstanceOf(Request::class);
        $this->endRequest($request, 200, ['rows' => 123])->shouldBe($this->getWrappedObject());
        $logger->log(
            LogLevel::INFO,
            $message,
            Argument::type('array')
        )->shouldHaveBeenCalled();
    }

    function it_can_replace_placeholders_in_messages()
    {
        $log = new Logger('Test logger');
        $file = dirname(__FILE__) . '/tmp.log';
        if (file_exists($file)) {
            unlink($file);
        }
        $log->pushHandler(new StreamHandler($file, Logger::INFO));
        $this->beConstructedWith($log);
        $this->trackMetric('There are {value} open files.', 23)->shouldBe($this->getWrappedObject());
        $content = file_get_contents($file);
        if (strpos($content, 'There are 23 open files.') === false) {
            throw new FailureException(
                "Expected log to have interpolated values in message, but it hasn't..."
            );
        }
    }

    function it_can_override_the_log_level(LoggerInterface $logger)
    {
        $message = 'Memory';
        $value = 234632;
        $count = 10;
        $min = 234600;
        $max = 234690;
        $stdDev = 34;
        $this->trackMetric($message, $value, $count, $min, $max, $stdDev, ['level' => LogLevel::WARNING]);
        $logger->log(
            LogLevel::WARNING,
            $message,
            array_merge(
                ['label' => Trackable::LABEL_METRIC],
                compact('value', 'count', 'min', 'max', 'stdDev')
            )
        )->shouldHaveBeenCalled();
    }

}

class EventWasUsed extends AbstractEvent implements SlickEvent, \JsonSerializable
{
    public function __construct()
    {
        parent::__construct();
    }

    public function jsonSerialize(): string
    {
        return 'it runs';
    }
}
