<?php

namespace Autobus\Bundle\BusBundle\Converter;

use Autobus\Bundle\BusBundle\Repository\WebJobRepository;
use Autobus\Bundle\BusBundle\Routing\Matcher\JobUrlMatcher;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Autobus\Bundle\BusBundle\Entity\WebJob;
use Symfony\Component\Routing\Route;

/**
 * Class JobParamConverter
 */
class JobParamConverter implements ParamConverterInterface
{
    /**
     * @var JobUrlMatcher
     */
    private $urlMatcher;

    /**
     * @var EntityManagerInterface
     */
    private $objectManager;

    /**
     * @param JobUrlMatcher $urlMatcher
     * @param EntityManagerInterface $objectManager
     */
    public function __construct(JobUrlMatcher $urlMatcher, EntityManagerInterface $objectManager)
    {
        $this->urlMatcher = $urlMatcher;
        $this->objectManager = $objectManager;
    }


    public function apply(Request $request, ParamConverter $configuration)
    {
        $routeParams = $request->attributes->get('_route_params');
        $path = $prefix = $routeParams['path'];
        $pathCollection = explode('/', $path);
        if (count($pathCollection) > 2) {
            $prefix = sprintf('%s', $pathCollection[1]);
        }

        /** @TODO : Reduce find requirement */
        $webJobs = $this->getWebJobRepository()->findAllMatchingPrefix($prefix);
        /** @var WebJob $webJob */
        foreach ($webJobs as $webJob) {
            $this->urlMatcher->addRoute(
                new Route(
                    str_replace($path, '', $request->getPathInfo()).$webJob->getPath(),
                    array('job' => $webJob),
                    array(),
                    array(),
                    '',
                    array(),
                    $webJob->getMethods()
                )
            );
        }
        $routeInfo = $this->urlMatcher->matchRequest($request);

        $request->attributes->set('job', $routeInfo['job']);
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === WebJob::class;
    }

    /**
     * @return WebJobRepository
     */
    private function getWebJobRepository()
    {
        return $this->objectManager->getRepository(WebJob::class);
    }
}
