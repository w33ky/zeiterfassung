<?php

namespace AppBundle\Controller;

use AppBundle\Container\WorkdayContainer;
use AppBundle\Entity\Workday;
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
        $date = new \DateTime();
        $year = $date->format('Y');
        $month = $date->format('m');

        return $this->userAction_date($user_id, $year, $month);
    }

    /**
     * @Route("/user/{user_id}/{year}/{month}", name="user_date")
     */
    public function userAction_date($user_id, $year, $month)
    {
        $em = $this->getDoctrine()->getManager();
        $repository_user = $em->getRepository('AppBundle:Person');
        $repository_workday = $em->getRepository('AppBundle:Workday');
        $repository_worktime = $em->getRepository('AppBundle:Worktime');
        $user = $repository_user->find($user_id);

        if ($month < 1) {
            return $this->redirect('/user/'.$user_id.'/'.($year-1).'/12');
        }

        if ($month > 12) {
            return $this->redirect('/user/'.$user_id.'/'.($year+1).'/1');
        }

        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        //TODO: custom SQL query
        $workdays = $repository_workday->findBy(array('person' => $user_id));

        $workday_data = [];

        for ($i = 1; $i <= $days_in_month; $i++) {
            $day = new \DateTime();
            $day->setDate($year, $month, $i);

            $found_day = false;
            $workday_container = new WorkdayContainer();
            /* @var $workday \AppBundle\Entity\Workday */
            foreach ($workdays as $workday) {
                if ($workday->getDate()->format('d-m-Y') == $day->format('d-m-Y')) {
                    $found_day = true;

                    $worktimes = $repository_worktime->findBy(array('workday' => $workday->getId()));
                    $time = 0;
                    $dt_max = null;
                    $dt_min = null;
                    /* @var $worktime \AppBundle\Entity\Worktime */
                    foreach ($worktimes as $worktime) {
                        $time_from = $worktime->getTimeFrom();
                        $time_to = $worktime->getTimeTo();
                        $time += $time_to->getTimestamp() - $time_from->getTimestamp();

                        if ($dt_min == null) $dt_min = $time_from;
                        if ($dt_max == null) $dt_max = $time_to;

                        if($dt_min > $time_from) $dt_min = $time_from;
                        if($dt_max < $time_to) $dt_max = $time_to;
                    }
                    $dt_from = new \DateTime();
                    $dt_from->setTimestamp(0);
                    $dt_to = new \DateTime();
                    $dt_to->setTimestamp($time);
                    $diff = $dt_from->diff($dt_to);
                    $time_string = $diff->format('%h:%I');
                    $time_string_max = $dt_max->format('H:i');
                    $time_string_min = $dt_min->format('H:i');

                    $workday_container = new WorkdayContainer();
                    $workday_container->workday_date = $workday->getDate()->format('d.m.Y');
                    $workday_container->worked_time = $time_string;
                    $workday_container->worked_from = '??:??';
                    $workday_container->worked_to = '??:??';
                    $workday_container->notes = $workday->getNote();
                    $workday_container->sick = $workday->getSick();
                    $workday_container->vacation = $workday->getVacation();
                    $workday_container->worked_from = $time_string_min;
                    $workday_container->worked_to = $time_string_max;
                    $workday_container->color = 0;
                    $workday_container->empty = false;
                }
            }

            if ($found_day == false) {
                $workday_container->workday_date = $day->format('d.m.Y');
                $workday_container->empty = true;
                $numeric_day = $day->format('w');
                if ($numeric_day == 0 || $numeric_day == 6) {
                    //Weekend
                    $workday_container->color = 2;
                }
                else $workday_container->color = 1;
            }

            $workday_data[$i] = $workday_container;
        }

        return $this->render('user.html.twig', array('user' => $user, 'workday_data' => $workday_data, 'year' => $year, 'month' => $month));
    }

    /**
     * @Route("/useredit/{user_id}", name="useredit")
     */
    public function userEditAction(Request $request, $user_id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository_user = $em->getRepository('AppBundle:Person');
        $user = $repository_user->find($user_id);
        $changed = false;

        $username = $request->get('user');
        $email = $request->get('email');

        if ($username != null) {
            $user->setName($username);
            $changed = true;
        }

        if ($email != null) {
            $user->setEmail($email);
            $changed = true;
        }

        $em->flush();

        return $this->render('useredit.html.twig', array('user' => $user, 'changed' => $changed));
    }
}
