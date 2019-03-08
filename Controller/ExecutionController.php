<?php

namespace Autobus\Bundle\BusBundle\Controller;

use Autobus\Bundle\BusBundle\Entity\Execution;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Execution controller.
 */
class ExecutionController extends AbstractController
{
    /**
     * Finds and displays a service call entity.
     *
     * @param Execution $execution
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Execution $execution)
    {
        return $this->render(
            'AutobusBusBundle::execution/show.html.twig',
            array(
                'execution' => $execution,
            )
        );
    }
}
