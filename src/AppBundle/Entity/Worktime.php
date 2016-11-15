<?php
/**
 * Created by PhpStorm.
 * User: w33ky
 * Date: 09.11.16
 * Time: 02:56
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="worktime")
 */
class Worktime
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $workday;

    /**
     * @ORM\Column(type="time")
     */
    private $time_from;

    /**
     * @ORM\Column(type="time")
     */
    private $time_to;

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
     * Set workday
     *
     * @param integer $workday
     *
     * @return Worktime
     */
    public function setWorkday($workday)
    {
        $this->workday = $workday;

        return $this;
    }

    /**
     * Get workday
     *
     * @return integer
     */
    public function getWorkday()
    {
        return $this->workday;
    }

    /**
     * Set timeFrom
     *
     * @param \DateTime $timeFrom
     *
     * @return Worktime
     */
    public function setTimeFrom($timeFrom)
    {
        $this->time_from = $timeFrom;

        return $this;
    }

    /**
     * Get timeFrom
     *
     * @return \DateTime
     */
    public function getTimeFrom()
    {
        return $this->time_from;
    }

    /**
     * Set timeTo
     *
     * @param \DateTime $timeTo
     *
     * @return Worktime
     */
    public function setTimeTo($timeTo)
    {
        $this->time_to = $timeTo;

        return $this;
    }

    /**
     * Get timeTo
     *
     * @return \DateTime
     */
    public function getTimeTo()
    {
        return $this->time_to;
    }
}
