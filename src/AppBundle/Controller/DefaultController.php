<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Worktime;
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
        $repository_worktime = $em->getRepository('AppBundle:Worktime');
        $user = $repository_user->find($user_id);
        $workdays = $repository_workday->findBy(array('person' => $user_id));

        $workday_data = [];
        foreach ($workdays as $workday) {
            $worktimes = $repository_worktime->findBy(array('workday' => $workday->getId()));
            $time = 0;
            /* @var $worktime \AppBundle\Entity\Worktime */
            foreach ($worktimes as $worktime) {
                $time_from = $worktime->getTimeFrom();
                $time_to = $worktime->getTimeTo();
                $time += $time_to->getTimestamp() - $time_from->getTimestamp();
            }
            $dt_from = new \DateTime();
            $dt_from->setTimestamp(0);
            $dt_to = new \DateTime();
            $dt_to->setTimestamp($time);
            $diff = $dt_from->diff($dt_to);
            $time_string = $diff->format('%h:%I');
            $workday_data[] = [$workday, $time_string];
        }

        return $this->render('user.html.twig', array('user' => $user, 'workday_data' => $workday_data));
    }
}
