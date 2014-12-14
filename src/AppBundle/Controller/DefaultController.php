<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        // If user is logged in, redirection to the activity page
        if ($this->getUser()) {
            return $this->redirect($this->generateUrl('activity_index'));
        }

        return $this->render('AppBundle:Homepage:index.html.twig');
    }

}
