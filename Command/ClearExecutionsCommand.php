<?php

namespace Autobus\Bundle\BusBundle\Command;

use Autobus\Bundle\BusBundle\Entity\Execution;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clear executions from database
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Command
 */
class ClearExecutionsCommand extends Command
{
    /**
     * Executions days to keep
     *
     * @var int
     */
    const DEFAULT_DAYS = 30;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ClearExecutionsCommand constructor.
     *
     * @param string|null            $name
     * @param ContainerInterface     $container
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(string $name = null, ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->container     = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('autobus:executions:clear')
            ->setDescription('Clear executions from database')
            ->addOption('days', 'd', InputOption::VALUE_OPTIONAL, 'Executions days to keep', self::DEFAULT_DAYS);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days         = $input->getOption('days');
        $dateInterval = new \DateInterval(sprintf('P%sD', $days));
        $date         = new \DateTime();
        $date->sub($dateInterval);

        // Get old executions
        /** @var Execution[] $executions */
        $executions      = $this->entityManager->getRepository('AutobusBusBundle:Execution')->getBeforeDate($date);
        $executionsCount = count($executions);
        foreach ($executions as $execution) {
            $this->entityManager->remove($execution);
        }
        $this->entityManager->flush();
        $output->writeln(sprintf('%s executions deleted.', $executionsCount));
    }
}
