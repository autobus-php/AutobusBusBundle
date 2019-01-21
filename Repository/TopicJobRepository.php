<?php

namespace Autobus\Bundle\BusBundle\Repository;

use Autobus\Bundle\BusBundle\Entity\TopicJob;

/**
 * Class TopicJobRepository
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Repository
 */
class TopicJobRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get topic jobs by $topics names
     *
     * @param array $topics
     *
     * @return TopicJob[]
     */
    public function getByTopics($topics)
    {
        return $this->createQueryBuilder('t')
                    ->where('t.topic IN(:topics)')
                    ->setParameter('topics', $topics)
                    ->getQuery()
                    ->execute();
    }
}
