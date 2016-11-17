<?php
/**
 * Created by PhpStorm.
 * User: w33ky
 * Date: 17.11.16
 * Time: 17:55
 */

namespace AppBundle;

use AppBundle\Entity\Person;
use AppBundle\Entity\Workday;
use AppBundle\Entity\Worktime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CsvHandler
{
    function loadCSV($em) {
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
        //$em = $this->getDoctrine()->getManager();
        $users = array();
        foreach ($csv as $array) {
            if (!in_array($array[0], $users)) {
                array_push($users, $array[0]);

                $check = $em->getRepository('AppBundle:Person')->find($array[0]);

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
        $repository = $em->getRepository('AppBundle:Workday');
        $repository2 = $em->getRepository('AppBundle:Worktime');
        foreach ($users as $user) {
            $csv_by_user = array();
            foreach ($csv as $item) {
                if ($array[0] == $user) {
                    array_push($csv_by_user, $item);
                }
            }

            //TODO: find right exception
            //if (count($csv_by_user % 2 == 1))
            //    throw $this->createNotFoundException();

            $worktime = new Worktime();
            foreach ($csv_by_user as $item) {
                //prepare the timestamp for later use
                $timestamp = $item[1];
                $timestamp_expl = explode(' ', $timestamp);
                $date = $timestamp_expl[0];
                $date_expl = explode('.', $date);
                $time = $timestamp_expl[1];
                $time_expl = explode(':', $time);

                $dt_date = new \DateTime();
                $dt_date->setDate($date_expl[2], $date_expl[1], $date_expl[0]);

                $dt_time = new \DateTime();
                $dt_time->setTime($time_expl[0], $time_expl[1]);

                //load or create the workday
                $workday = $repository->findOneBy(array('person' => $user, 'date' => $dt_date));
                if ($workday == null) {
                    $workday = new Workday();
                    $workday->setPerson($user);
                    $workday->setDate($dt_date);
                    $workday->setNote('');
                    $workday->setSickOrVacation('');
                    $em->persist($workday);
                    $em->flush();
                    $workday = $repository->findOneBy(array('person' => $user, 'date' => $dt_date));
                }

                //check if work-time exists already
                $check = $repository2->findOneBy(array('workday' => $workday->getId(), 'time_from' => $dt_time));
                if ($check != null) continue;
                $check = $repository2->findOneBy(array('workday' => $workday->getId(), 'time_to' => $dt_time));
                if ($check != null) continue;

                //create work-time
                if ($worktime->getTimeFrom() == null){
                    $worktime->setWorkday($workday->getId());
                    $worktime->setTimeFrom($dt_time);
                }
                else {
                    $worktime->setTimeTo($dt_time);
                    $em->persist($worktime);
                    $em->flush();
                    $worktime = new Worktime();
                }
            }
        }
    }
}