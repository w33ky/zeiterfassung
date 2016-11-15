<?php
/**
 * Created by PhpStorm.
 * User: w33ky
 * Date: 08.11.16
 * Time: 20:39
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Entity\Workday;
use AppBundle\Entity\Worktime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CSVController extends Controller
{
    /**
     * @Route("/csv", name="csv")
     */
    function loadCSV() {
        //load data from file
        $csv = array();
        if (($handle = fopen("timelog.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                array_push($csv, $data);
            }
            fclose($handle);
        }

        //clean dataset
        $new_csv = array();
        foreach ($csv as $array) {
            $new_array = array();
            $pos = 1;
            foreach ($array as $item) {
                if ($item != '' && $pos != 1) {
                    array_push($new_array, $item);
                }
                $pos++;
            }
            array_push($new_csv, $new_array);
        }
        $csv = $new_csv;

        //write users into database
        $em = $this->getDoctrine()->getManager();
        $users = array();
        foreach ($csv as $array) {
            if (!in_array($array[0], $users)) {
                array_push($users, $array[0]);

                $check = $this->getDoctrine()->getRepository('AppBundle:Person')->find($array[0]);

                if ($check == null) {
                    $person = new Person();
                    $person->setId($array[0]);
                    $person->setName("Test Person " . count($users));
                    $em->persist($person);
                }
            }
        }
        $em->flush();

        //write timestamps into database
        $repository = $this->getDoctrine()->getRepository('AppBundle:Workday');
        foreach ($users as $user) {
            $csv_by_user = array();
            foreach ($csv as $item) {
                if ($array[0] == $user) {
                    array_push($csv_by_user, $item);
                }
            }

            if (count($csv_by_user % 2 == 1))
                throw $this->createNotFoundException();

            $i = 1;
            foreach ($csv_by_user as $item) {
                $timestamp = $item[1];
                $timestamp_expl = explode(' ', $timestamp);
                $date = $timestamp_expl[0];
                $time = $timestamp_expl[1];

                $workday = $repository->findBy(array('person' => $user, 'date' => $date));
                if ($workday == null) {
                    $workday = new Workday();
                    $workday->setPerson($user);
                    $workday->setDate($date);
                    $em->persist($workday);
                }

                $worktime = new Worktime();
                $worktime->setWorkday($workday->getId());
                $worktime->setTimeFrom($time);
            }
        }


        return $this->render('csv.html.twig', array('data' => $csv));
    }
}