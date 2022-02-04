# Slick JSON API implementation package

[![Latest Version](https://img.shields.io/github/release/slickframework/telemetry.svg?style=flat-square)](https://github.com/slickframework/telemetry/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/slickframework/telemetry/Continuous%20Integration?style=flat-square)](https://github.com/slickframework/telemetry/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/slickframework/telemetry/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/slickframework/telemetry?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/slick/telemetry.svg?style=flat-square)](https://packagist.org/packages/slick/telemetry)

`slick/telemetry` is a small library that uses a PSR-3 compliant logger to send HTTP/Application
telemetry metrics to a given log service/system. It abstracts the concepts of a _metric_, _dependency_,
_exception_, PSR-14 slick implementation of an _event_ and HTTP _requests_. It also implements
[PSR-3]`LoggerInterface` allowing its usage as a regular logger.

This package is compliant with PSR-12 code standards and PSR-4 autoload standards. It
also applies the [semantic version 2.0.0](http://semver.org) specification.

## Install

Via Composer

```bash
$ composer require slick/telemetry
```

## Usage
To start using the telemetry logger you will need a [PSR-3] implementation that will
deliver the information to your preferred log system.<br />
We recommend `monolog\monolog` package, developed by [Jordi Boggiano] with one
of its [handlers]. Please visit [Monolog] project site on [GitHub] for more information.

To add `monolog\monolog` to you project run the following:

```bash
$ composer require monolog\monolog
```

#### Create a telemetry client
With a [PSR-3] logger we are now ready to create our client:

```php
<?php

namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slick\Telemetry\TelemetryClient;

$logger = new Logger('my-app');
$logger->pushHandler(
    new StreamHandler('path/to/your.log', Logger::INFO)
);

$telemetryClient = new TelemetryClient($logger);
```

#### Register a metric
There are 2 types of metric telemetry: single measurement and pre-aggregated metric. Single
measurement is just a name and value. Pre-aggregated metric specifies minimum and maximum
value of the metric in the aggregation interval and standard deviation of it.

```php
<?php

$telemetryClient->trackMetric("Open processes", 345); // measurement
$telemetryClient->tracMetric("Memory", 89.43, 8, 60.3, 94.323, 5) // Pre-aggregated metric

// adding more context to the measurement
$telemetryClient->trackMetric("Open processes", 345, context: ["region" => "north"]);

```

#### Register a dependency
Dependency represents an interaction of the application with a remote component such as SQL
or an HTTP endpoint.

```php
<?php

// A SQL example of a dependency entry
$telemetryClient->trackDependency(
    "Query user tasks list",
    "SQL"
    "select * from tasks where user_id = :uid",
    1643808614,
    2.33793258,
    true,
    [
        "database" => "Local MySQL DB",
        "rows" => 23,
        "uid" => 212
    ]
);
```

#### Register an exception
Exception represents a handled or unhandled exception that occurred during
execution of the monitored application.
```php
<?php
// Handled exception
try {
    // faulty code
} catch (\Exception $e) {
    $telemetryClient->trackException($e);
}

// Unhandled exception
function exception_handler($exception) {
  $telemetryClient->trackException($e);
}

set_exception_handler('exception_handler');
throw new Exception('Uncaught Exception');
```

#### Register an event
Event telemetry represent an event that occurred in your application.
Typically it is a user interaction such as button click or order checkout.
It can also be an application life cycle event like initialization or
configuration update.

Semantically, events may or may not be correlated to requests. However, if
used properly, event telemetry is more important than requests.
Events represent business telemetry.

In order to use this telemetry item, you need to create events with
[`slick/event`](https://github.com/slickframework/event), witch is a required
dependency for this library. [`slick/event`](https://github.com/slickframework/event)
is a simple PSR-14 event handling implementation library.
We also recommend that event objects should implement the ``JsonSerializable`` interface so that
the data can be passed as context to the log service.
```php
<?php

namespace App;
 
use Slick\Event\Domain\AbstractEvent;
use Slick\Event\Event as SlickEvent;

/**
 * Event class example
 */
class TaskWasFinished extends AbstractEvent implements SlickEvent, \JsonSerializable
{
    public function __construct(private int $taskId)
    {
        parent::__construct();
    }
    
    public function jsonSerializa()
    {
        return ['taskId' => $this->taskId];
    }
}

// track the event
$telemetryClient->trackEvent(new TaskWasFinished(34));
```

#### Register a HTTP request
An HTTP request can be used to register information of a request: path,
duration, post data or headers can be registered here.

##### Single call
```php
<?php

$telemetryClient->trackRequest(
    'create user',
    '/users',
    1643887671,
    201,
    276.4379,
    $_REQUEST
);
```

##### Request factory methods
With these methods we can let the telemetry client calculate time and duration for us.
This is done by registering the difference between `beginRequest()` and `endRequest()`
method calls.

Let's see an example:
```php
<?php
// front-controller script
$trackableRequest = $telemetryClient->beginRequest('create user', '/users', $_REQUEST);

// front-controller execution code

$telemetryClient->endRequest($trackableRequest, 201);
```
This code as the same effect as the one make with a single call
to `TelemetryClient::trackRquest()` method

#### Other features
##### Message interpolation
In all calls to `TelemetryClient::track()` or `TelemetryClient::log()` methods you set placeholders
in the message parameter so that they can be replaced by values from the context array. You need to
follow these rules:
 - Placeholder names MUST correspond to keys in the context array;
 - Placeholder names MUST be delimited with a single opening brace `{` and a single closing brace `}`;
 - There MUST NOT be any whitespace between the delimiters and the placeholder name.

```php
<?php

$telemetryClient->notice(
    "User {name}, was suspended because {reason}.",
    ["name" => "John Doe", "he had too many login attempts"]
);

// Resulting message will be:
// User John Doe, was suspended because he had too many login attempts.
```

##### Log level override
All methods have a [logLevel] assign by default. For all the default is `LogLevel::INFO`
except for `TrackableException` where the default is `LogLevel::ERROR`.

In addition `Dependency` and `Request` can have `LogLevel::WARNING` if its `isSuccessfull` property
is false.

You can change this log level if you pass the `level` key to the context parameter:
````php
<?php

// Override LogLevel::INFO with LogLevel::WARNING in a metric register
$telemeteryClient->trackMetric('free space', 20, context: ['level' => LogLevel::WARNING]);
````

## Testing

We use [PHPSpec](http://www.phpspec.net/) for unit testing.

```bash
# unit tests with phpspec
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email slick.framework@gmail.com instead of using the issue tracker.

## Credits

- [Slick framework](https://github.com/slickframework)
- [All Contributors](https://github.com/slickframework/json-api/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[PSR-3]: https://www.php-fig.org/psr/psr-3
[Jordi Boggiano]: https://github.com/Seldaek
[handlers]: https://github.com/Seldaek/monolog/blob/main/doc/02-handlers-formatters-processors.md
[Monolog]: https://github.com/Seldaek/monolog
[GitHub]: https://github.com/Seldaek/monolog
[logLevel]: https://github.com/php-fig/log/blob/master/src/LogLevel.php
