<?php

namespace Autobus\Bundle\BusBundle\Event;

/**
 * Class RunnerEvents
 */
final class RunnerEvents
{
    const BEFORE_HANDLE = 'bus.runner.before_handle';

    const AFTER_HANDLE = 'bus.runner.after_handle';

    const ERROR = 'bus.runner.error';

    const SUCCESS = 'bus.runner.success';
}
