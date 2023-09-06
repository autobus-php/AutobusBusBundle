# Autobus Bus Bundle - C&M

> A PHP Extensible Service Bus

Configure your atomic jobs in order to be played by cron, queue listener or webservice.

## Requirements

 - PHP 8+

## Installation

### Download the Bundle

```console
composer require autobus-php/autobus-bus-bundle
```

### Download desired queuing bundle

* If you want to use **Google PubSub** (https://cloud.google.com/pubsub/docs/overview):
```console
composer require enqueue/gps:0.10.*
```

* If you want to use **AWS SQS** (https://aws.amazon.com/fr/sqs):
```console
composer require enqueue/sqs:0.10.*
```

### Environment variables

Update your `.env` file to add:
```
# 'gps:' for Google PubSub / 'sqs:' for AWS SQS 
ENQUEUE_DSN=sqs:

# Only for AWS SQS usage
AWS_REGION=
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
```

## Configuration

### Topic jobs

2 queuing systems are available:

* With **Google PubSub**:

Execute the following command with **Supervisor** tool:
```
php bin/console autobus:pubsub:consume
```

* With **AWS SQS**:

Execute the following command with **Supervisor** tool:
```
php bin/console autobus:sqs:consume
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
