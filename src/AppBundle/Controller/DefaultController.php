<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\CsvHandler;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $csvHandler = new CsvHandler();
        $csvHandler->loadCSV($em);

        $repository = $em->getRepository('AppBundle:Person');
        $users = $repository->findAll();

        return $this->render('csv.html.twig', array('users' => $users));
    }

    /**
     * @Route("/user/{user_id}", name="user")
     */
    public function userAction($user_id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository_user = $em->getRepository('AppBundle:Person');
        $repository_workday = $em->getRepository('AppBundle:Workday');
        $user = $repository_user->find($user_id);
        $workdays = $repository_workday->findBy(array('person' => $user_id));

        return $this->render('user.html.twig', array('user' => $user, 'workdays' => $workdays));
    }
}
