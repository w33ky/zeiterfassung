<?php
/**
 * Created by PhpStorm.
 * User: w33ky
 * Date: 08.11.16
 * Time: 20:39
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CSVController extends Controller
{
    /**
     * @Route("/csv", name="csv")
     */
    function loadCSV() {
        return $this->render('csv.html.twig', array('data' => $csv));
    }
}