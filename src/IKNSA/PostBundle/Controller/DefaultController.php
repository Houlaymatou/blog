<?php

namespace IKNSA\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('IKNSAPostBundle:Default:index.html.twig');
    }
}
