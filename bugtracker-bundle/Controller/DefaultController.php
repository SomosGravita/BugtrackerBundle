<?php

namespace Elemento115\BugtrackerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BugtrackerBundle:Default:index.html.twig');
    }
}
