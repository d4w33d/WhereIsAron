<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Activity;
use AppBundle\Utility;

class ActivityController extends Controller
{

    /**
     * @Route("/activity", name="activity_index")
     */
    public function indexAction(Request $request)
    {
        $activity = $this->getCurrentActivity();

        // If no current activity found, let's show the creation form
        if (!$activity) {
            return $this->redirect($this->generateUrl('activity_create'));
        }

        return $this->render('AppBundle:Activity:current.html.twig', array(
            'activity' => $activity,
            'delay_values' => $this->getDelayValues()
        ));
    }

    /**
     * @Route("/activity/create", name="activity_create")
     */
    public function createAction(Request $request)
    {
        $activity = new Activity();

        $form = $this->createFormBuilder($activity)
            ->setAction($this->generateUrl('activity_create'))
            ->setMethod('POST')
            ->add('name', 'text')
            ->add('description', 'textarea')
            ->add('locations', 'textarea')
            ->add('contacts', 'textarea')
            ->add('ping_delay', 'choice', array(
                'choices' => $this->getDelayValues()
            ))
            ->add('save', 'submit')
            ->getForm();

        // The form was not submitted, so only display it.
        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->render('AppBundle:Activity:create.html.twig', array(
                'form' => $form->createView()
            ));
        }

        // POST method, so process the form
        $form->handleRequest($request);

        // If form errors, display it (errors will be shown in the template)
        if (!$form->isValid()) {
            return $this->render('AppBundle:Activity:create.html.twig', array(
                'form' => $form->createView()
            ));
        }

        // That's a success. Finish to fill it then store it.
        $activity = $form->getData();
        $activity->setIdUser($this->getUser()->getId());
        $activity->setBeginsAt(new \DateTime());
        $activity->setLastPingAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($activity);
        $em->flush();

        // Finaly, redirect to activity page to show the result
        return $this->redirect($this->generateUrl('activity_index'));
    }

    /**
     * @Route("/activity/ping", name="activity_ping")
     */
    public function pingAction(Request $request)
    {
        if (!($activity = $this->getCurrentActivity())) {
            throw $this->createNotFoundException('Aucune activité en cours');
        }

        $activity->setLastPingAt(new \DateTime());

        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('activity_index'));
    }

    /**
     * @Route("/activity/end", name="activity_end")
     */
    public function endAction(Request $request)
    {
        if (!($activity = $this->getCurrentActivity())) {
            throw $this->createNotFoundException('Aucune activité en cours');
        }

        $activity->setEndsAt(new \DateTime());

        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('activity_index'));
    }

    /**
     * Retrieve current activity of the logged in user
     *
     * @return AppBundle\Entity\Activity
     */
    private function getCurrentActivity()
    {
        return $this->getDoctrine()
            ->getRepository('AppBundle:Activity')
            ->getCurrentByUser($this->getUser()->getId());
    }

    /**
     * Returns an aray containing allowed delays used for ping timeout
     *
     * @return array
     */
    private function getDelayValues()
    {
        $seconds = array(
            60, 60 * 5, 60 * 10, 60 * 15, 60 * 30,
            3600, 3600 * 2, 3600 * 4, 3600 * 6, 3600 * 12,
            86400, 86400 * 2, 86400 * 3, 86400 * 5, 86400 * 7, 86400 * 14
        );

        $delays = array();
        foreach ($seconds as $ss) {
            $delays[$ss] = Utility::formatDateInterval($ss,
                $this->get('translator'));
        }

        return $delays;
    }

}
