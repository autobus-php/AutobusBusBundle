<?php

namespace Autobus\Bundle\BusBundle\Entity;

use Cron\CronExpression;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * CronJob
 *
 * @ORM\Entity(repositoryClass="Autobus\Bundle\BusBundle\Repository\CronJobRepository")
 */
class CronJob extends Job
{
    /**
     * @var array
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $schedule;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $nextRunDate;

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     * @param $payload
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (!CronExpression::isValidExpression($this->getSchedule())) {
            $context->buildViolation('Invalid schedule format (should be cron-compliant)')
              ->atPath('schedule')
              ->addViolation();
        }
    }

    /**
     * @param array $schedule
     * @return CronJob
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * @return array
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @param string $nextRunDate
     * @return CronJob
     */
    public function setNextRunDate($nextRunDate)
    {
        $this->nextRunDate = $nextRunDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getNextRunDate()
    {
        return $this->nextRunDate;
    }

    /**
     * @param \DateTime $date
     */
    public function reschedule($date = null)
    {
        if (is_null($date)) {
            $date = CronExpression::factory($this->getSchedule())->getNextRunDate();
        }

        $this->setNextRunDate($date);
    }
}
