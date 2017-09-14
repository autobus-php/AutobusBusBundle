<?php

namespace Autobus\Bundle\BusBundle\Model;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * CronJobInterface
 */
interface CronJobInterface
{
    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     * @param $payload
     */
    public function validate(ExecutionContextInterface $context, $payload);

    /**
     * @param array $schedule
     * @return Job
     */
    public function setSchedule($schedule);

    /**
     * @return array
     */
    public function getSchedule();

    /**
     * @param string $nextRunDate
     * @return Job
     */
    public function setNextRunDate($nextRunDate);

    /**
     * @return string
     */
    public function getNextRunDate();

    /**
     * @param \DateTime $date
     */
    public function reschedule($date = null);
}
