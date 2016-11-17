<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\CsvHandler;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $csvHandler = new CsvHandler();
        $csvHandler->loadCSV($em);

        $repository = $em->getRepository('AppBundle:Person');
        $users = $repository->findAll();

        return $this->render('csv.html.twig', array('users' => $users));
    }
}
