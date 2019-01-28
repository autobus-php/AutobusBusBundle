<?php

namespace Autobus\Bundle\BusBundle\Controller;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Entity\JobFactory;
use Autobus\Bundle\BusBundle\Entity\WebJob;
use Autobus\Bundle\BusBundle\Form\JobTypeFactory;
use Autobus\Bundle\BusBundle\Repository\ExecutionRepository;
use Autobus\Bundle\BusBundle\Runner\RunnerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class JobController
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Controller
 */
class JobController extends AbstractController
{
    /**
     * Executions per page on job show
     *
     * @var int
     */
    const PAGINATION_EXECUTIONS_PER_PAGE = 20;

    /**
     * Lists all job entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $jobs = $em->getRepository('AutobusBusBundle:Job')->findAll();

        return $this->render('AutobusBusBundle::job/index.html.twig', array(
            'jobs' => $jobs,
        ));
    }

    /**
     * Creates a new service entity.
     *
     * @param Request        $request
     * @param JobFactory     $jobFactory
     * @param JobTypeFactory $jobTypeFactory
     */
    public function newAction(Request $request, JobFactory $jobFactory, JobTypeFactory $jobTypeFactory)
    {
        $type = $request->get('job_type', '');
        if (empty($type)) {
            return $this->render('AutobusBusBundle::job/new.html.twig', []);
        }

        $job      = $jobFactory->create($type);
        $formType = $jobTypeFactory->create($job);
        $runnerClasses = [];

        foreach ($jobTypeFactory->getRunners() as $runner) {
            $runnerClasses[] = get_class($runner);
        }

        $form = $this->createForm(
            get_class($formType),
            $job,
            ['runners' => array_flip($runnerClasses)]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('job_show', array('id' => $job->getId()));
        }

        return $this->render('AutobusBusBundle::job/new.html.twig', array(
            'job'  => $job,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a job entity.
     *
     * @param Job $job
     * @param int $page
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function showAction(Job $job, $page, PaginatorInterface $paginator)
    {
        $deleteForm = $this->createDeleteForm($job);

        $em = $this->getDoctrine()->getManager();
        /** @var ExecutionRepository $executionRepository */
        $executionRepository = $em->getRepository('AutobusBusBundle:Execution');
        $executionsQuery     = $executionRepository->getQueryByJob($job);
        $executions          = $paginator->paginate(
            $executionsQuery,
            $page,
            self::PAGINATION_EXECUTIONS_PER_PAGE
        );

        return $this->render('AutobusBusBundle::job/show.html.twig', array(
            'job'         => $job,
            'delete_form' => $deleteForm->createView(),
            'executions'  => $executions,
        ));
    }

    /**
     * Displays a form to edit an existing service entity.
     *
     */
    public function editAction(Request $request, Job $job, JobTypeFactory $jobTypeFactory)
    {
        $deleteForm = $this->createDeleteForm($job);
        $formType = $jobTypeFactory->create($job);
        $runnerClasses = [];

        foreach ($jobTypeFactory->getRunners() as $runner) {
            $runnerClasses[] = get_class($runner);
        }

        $editForm = $this->createForm(
            get_class($formType),
            $job,
            ['runners' => array_flip($runnerClasses)]
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('job_edit', array('id' => $job->getId()));
        }

        return $this->render('AutobusBusBundle::job/edit.html.twig', array(
            'job' => $job,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a service entity.
     *
     */
    public function deleteAction(Request $request, Job $job)
    {
        $form = $this->createDeleteForm($job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($job);
            $em->flush();
        }

        return $this->redirectToRoute('job_index');
    }

    /**
     * @param Request $request
     * @param WebJob $job
     * @param Execution $execution
     * @param Context $context
     *
     * @ParamConverter(converter="bus_job_converter", class="Autobus\Bundle\BusBundle\Entity\WebJob")
     * @return Response
     */
    public function executeAction(Request $request, WebJob $job, Execution $execution, Context $context)
    {
        $runnerServiceId = $job->getRunner();
        /** @var RunnerInterface $runner */
        $runner = $this->get($runnerServiceId);

        $response = new Response();
        $context->setRequest($request)->setResponse($response);

        $runner->handle($context, $job, $execution);

        if ($execution->mustBeSaved()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($execution);
            $em->flush();
        }

        return $context->getResponse();
    }

    /**
     * Creates a form to delete a service entity.
     *
     * @param Job $job The service entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Job $job)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('job_delete', array('id' => $job->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
