<?php
/**
 * Created by PhpStorm.
 * User: w33ky
 * Date: 09.11.16
 * Time: 01:38
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;

class SQLTest extends Controller
{
    /**
     * @Route("/sql", name="sql")
     */
    function test() {
        $person = new Person();
        $person->setId('04B8E69A983C80');
        $person->setName('Test User 1');

        $em = $this->getDoctrine()->getManager();
        $em->persist($person);

        $em->flush();

        return new Response('Saved new product with id '.$person->getId());
    }
}