<?php

namespace Autobus\Bundle\BusBundle\Runner;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Helper\JobHelper;
use Autobus\Bundle\BusBundle\Soap\Action\SoapAction;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SoapRunner
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Runner
 */
class SoapRunner extends WebRunner
{
    /**
     * Wsdl path key in job config array
     *
     * @var string
     */
    const CONFIG_WSDL_PATH = 'wsdlPath';

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
        // If is a wsdl request
        $request        = $context->getRequest();
        $this->wsdlPath = $this->loadWsdlPath($job);
        if ($request->query->has('wsdl')) {
            return $this->getWsdlResponse();
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
     * Load wsdl path from job parameter
     *
     * @param Job $job
     *
     * @return string
     */
    protected function loadWsdlPath(Job $job)
    {
        // Check for wsdl path in job configuration
        $config = $job->getConfigArray();
        if (array_key_exists(self::CONFIG_WSDL_PATH, $config)) {
            return $this->jobHelper->getPathFromRoot($config[self::CONFIG_WSDL_PATH]);
        }

        return null;
    }

    /**
     * Get wsdl response
     *
     * @return Response
     */
    protected function getWsdlResponse()
    {
        $response    = new Response();
        $wsdlContent = file_get_contents($this->wsdlPath);
        if ($wsdlContent !== false) {
            $response->headers->set('Content-Type', 'text/xml');
            $response->setContent($wsdlContent);
        }

        return $response->send();
    }

    /**
     * Return SOAP action
     *
     * @return SoapAction
     */
    protected function getSoapAction()
    {
        // TODO: Implement getSoapAction() method.
    }
}
