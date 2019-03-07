<?php

namespace Autobus\Bundle\BusBundle\Runner;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Helper\JobHelper;
use Autobus\Bundle\BusBundle\Soap\Action\SoapActionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * SoapRunner
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Runner
 */
abstract class SoapRunner extends WebRunner
{
    /**
     * Wsdl placeholder for the service location
     *
     * @var string
     */
    const HOSTNAME_PLACEHOLDER = '{HOSTNAME_PLACEHOLDER}';

    /**
     * @var JobHelper
     */
    protected $jobHelper;

    /**
     * Current wsdl path
     *
     * @var string
     */
    protected $wsdlPath;

    /**
     * SoapRunner constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param JobHelper                $jobHelper
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, JobHelper $jobHelper)
    {
        parent::__construct($eventDispatcher);
        $this->jobHelper = $jobHelper;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    protected function run(Context $context, Job $job, Execution $execution)
    {
        $request = $context->getRequest();

        // If is a wsdl request
        $this->wsdlPath = $this->jobHelper->getPathFromRoot(
            $this->jobHelper->loadWsdlPath($job)
        );
        if ($request->getMethod() === 'GET') {
            $execution->setMustBeSaved(false);
            $currentHostname = $request->getSchemeAndHttpHost();
            $response = $request->query->has('wsdl') ? $this->getWsdlResponse($currentHostname) : $this->getBadRequestResponse();
            $context->setResponse($response);

            return $response;
        }

        // Init SOAP server
        $soapServer = new \SoapServer($this->wsdlPath);
        $soapAction = $this->getSoapAction();
        $soapAction->setJob($job);
        $soapServer->setObject($soapAction);
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');
        ob_start();
        try {
            $soapServer->handle();
        } catch (\SoapFault $fault) {
            $soapServer->fault($fault->getCode(), $fault->getMessage());
        }
        $response->setContent(ob_get_clean());
        $context->setResponse($response);
        $context->setMessage($response->getContent());
    }

    /**
     * Get wsdl response
     *
     * @param string $currentHostname
     *
     * @return Response
     */
    protected function getWsdlResponse(string $currentHostname)
    {
        $response    = new Response();
        $wsdlContent = file_get_contents($this->wsdlPath);
        if ($wsdlContent !== false) {
            $wsdlContent = str_replace(self::HOSTNAME_PLACEHOLDER, $currentHostname, $wsdlContent);
            $response->headers->set('Content-Type', 'text/xml');
            $response->setContent($wsdlContent);
        }

        return $response;
    }

    /**
     * Get bad request response
     *
     * @return Response
     */
    protected function getBadRequestResponse()
    {
        $response = new Response();
        $response->setStatusCode(400);
        $response->setContent('Bad request');

        return $response;
    }

    /**
     * Return SOAP action
     *
     * @return SoapActionInterface
     */
    abstract protected function getSoapAction();
}
