<?php

namespace Stmol\HuddleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('StmolHuddleBundle:Default:index.html.twig', array('name' => $name));
    }
}
