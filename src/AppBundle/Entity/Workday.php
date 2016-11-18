<?php
/**
 * Created by PhpStorm.
 * User: w33ky
 * Date: 08.11.16
 * Time: 23:50
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="workday")
 */
class Workday
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=14)
     */
    private $person;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $note;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sick;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vacation;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set person
     *
     * @param string $person
     *
     * @return Workday
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return string
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Workday
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Workday
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @return mixed
     */
    public function getSick()
    {
        return $this->sick;
    }

    /**
     * @param mixed $sick
     */
    public function setSick($sick)
    {
        $this->sick = $sick;
    }

    /**
     * @return mixed
     */
    public function getVacation()
    {
        return $this->vacation;
    }

    /**
     * @param mixed $vacation
     */
    public function setVacation($vacation)
    {
        $this->vacation = $vacation;
    }


}
