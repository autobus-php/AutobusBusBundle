<?php

namespace Autobus\Bundle\BusBundle\Controller;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Entity\WebJob;
use Autobus\Bundle\BusBundle\Runner\RunnerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Job controller.
 *
 */
class JobController extends Controller
{
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
     */
    public function newAction(Request $request)
    {
        $type = $request->get('job_type', '');
        if (empty($type)) {
            return $this->render('AutobusBusBundle::job/new.html.twig', []);
        }

        $job = $this->get('bus.job.factory')->create($type);
        $formType = $this->get('bus.form.job.factory')->create($job);
        $form = $this->createForm(
            get_class($formType),
            $job,
            ['runner_chain' => $this->get('Autobus\Bundle\BusBundle\Runner\RunnerChain')]
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
     */
    public function showAction(Job $job)
    {
        $deleteForm = $this->createDeleteForm($job);

        return $this->render('AutobusBusBundle::job/show.html.twig', array(
            'job' => $job,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing service entity.
     *
     */
    public function editAction(Request $request, Job $job)
    {
        $deleteForm = $this->createDeleteForm($job);
        $formType = $this->get('bus.form.job.factory')->create($job);
        $editForm = $this->createForm(
            get_class($formType),
            $job,
            ['runner_chain' => $this->get('Autobus\Bundle\BusBundle\Runner\RunnerChain')]
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
     * @param WebJob     $job
     *
     * @ParamConverter(converter="bus_job_converter", class="Autobus\Bundle\BusBundle\Entity\WebJob")
     * @return Response
     */
    public function executeAction(Request $request, WebJob $job)
    {
        $runnerServiceId = $job->getRunner();
        /** @var RunnerInterface $runner */
        $runner = $this->get($runnerServiceId);

        $response = new Response();
        $execution = new Execution();
        $context = new Context();
        $context->setRequest($request)->setResponse($response);

        $runner->handle($context, $job, $execution);

        $em = $this->getDoctrine()->getManager();
        $em->persist($execution);
        $em->flush();

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
