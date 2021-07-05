# Autobus Bus Bundle - C&M

> A PHP Extensible Service Bus

Configure your atomic jobs in order to be played by cron, queue listener or webservice.

## Requirements

 - PHP 8+

## Installation

### Download the Bundle

```console
$ composer require autobus-php/autobus-bus-bundle
```

## Configuration

### Topic jobs

Two queuing systems are available:

* With **Enqueue** library and usage of RabbitMQ for example:

Add the following line to your crontab:
```
* * * * * php bin/console enqueue:consume --setup-broker -vvv
``` 

* With **Google PubSub** library (https://cloud.google.com/pubsub/docs/overview):

Execute the following command with **Supervisor** tool (http://supervisord.org/):
```
php bin/console autobus:pubsub:consume
```

### Cron jobs

Add the following line to your crontab:
```
* * * * * php bin/console autobus:cron:run
```

## Usage

### Create a job

To create a new job:

* Create it's class, implementing `Autobus\Bundle\BusBundle\Runner\RunnerInterface` ; it may extend `Autobus\Bundle\BusBundle\Runner\AbstractRunner`
* Declare it as a service in your bundle's `services.yml`, with tag `bus.runner`
* Create an instance from the web UI
