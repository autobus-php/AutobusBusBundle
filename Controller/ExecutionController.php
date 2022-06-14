<?php

namespace Autobus\Bundle\BusBundle\Controller;

use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Entity\TopicJob;
use Autobus\Bundle\BusBundle\Queue\PubSubWriter as QueueWriter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Execution controller.
 */
class ExecutionController extends Controller
{
    /**
     * @var QueueWriter
     */
    protected $queueWriter;

    /**
     * @param JobUrlMatcher $urlMatcher
     */
    public function __construct(QueueWriter $queueWriter)
    {
        $this->queueWriter = $queueWriter;
    }

    /**
     * Finds and displays a service call entity.
     *
     * @param Request     $request
     * @param Execution   $execution
     * @param QueueWriter $queueWriter
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Execution $execution)
    {
        $job   = $execution->getJob();
        $reRun = $request->query->get('reRun', false);
        if (
            $reRun
            && $job instanceof TopicJob
        ) {
            $this->queueWriter->write($job->getTopic(), $execution->getRequest());
            $this->addFlash('success', 'Execution was scheduled');
        }

        return $this->render(
            '@AutobusBus/execution/show.html.twig',
            array(
                'execution' => $execution,
            )
        );
    }
}
