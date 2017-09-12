<?php

namespace Autobus\Bundle\BusBundle\Controller;

use Autobus\Bundle\BusBundle\Entity\Execution;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Execution controller.
 */
class ExecutionController extends Controller
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
