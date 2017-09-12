<?php

namespace Autobus\Bundles\Autobus\Bundle\BusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('Autobus\Bundles\Autobus\Bundle\BusBundle:Default:index.html.twig');
    }
}
