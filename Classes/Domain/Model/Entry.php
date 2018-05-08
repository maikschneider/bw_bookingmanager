<?php
namespace Blueways\BwBookingmanager\Domain\Model;

/***
 *
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 *
 ***/

/**
 * Entry
 */
class Entry extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * startDate
     *
     * @var \DateTime
     */
    protected $startDate = null;

    /**
     * endDate
     *
     * @var \DateTime
     */
    protected $endDate = null;

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * prename
     *
     * @var string
     */
    protected $prename = '';

    /**
     * street
     *
     * @var string
     */
    protected $street = '';

    /**
     * zip
     *
     * @var string
     */
    protected $zip = '';

    /**
     * city
     *
     * @var string
     */
    protected $city = '';

    /**
     * phone
     *
     * @var string
     */
    protected $phone = '';

    /**
     * email
     *
     * @var string
     */
    protected $email = '';

    /**
     * newsletter
     *
     * @var bool
     */
    protected $newsletter = false;

    /**
     * weight
     *
     * @var int
     */
    protected $weight = 0;

    /**
     * calendar
     *
     * @var \Blueways\BwBookingmanager\Domain\Model\Calendar
     */
    protected $calendar = null;

    /**
     * timeslot
     *
     * @var \Blueways\BwBookingmanager\Domain\Model\Timeslot
     */
    protected $timeslot = null;

    /**
     * Returns the startDate
     *
     * @return \DateTime $startDate
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Sets the startDate
     *
     * @param \DateTime $startDate
     * @return void
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Returns the endDate
     *
     * @return \DateTime $endDate
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Sets the endDate
     *
     * @param \DateTime $endDate
     * @return void
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the prename
     *
     * @return string $prename
     */
    public function getPrename()
    {
        return $this->prename;
    }

    /**
     * Sets the prename
     *
     * @param string $prename
     * @return void
     */
    public function setPrename($prename)
    {
        $this->prename = $prename;
    }

    /**
     * Returns the street
     *
     * @return string $street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Sets the street
     *
     * @param string $street
     * @return void
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * Returns the zip
     *
     * @return string $zip
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Sets the zip
     *
     * @param string $zip
     * @return void
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * Returns the city
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Returns the phone
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets the phone
     *
     * @param string $phone
     * @return void
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the newsletter
     *
     * @return bool $newsletter
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Sets the newsletter
     *
     * @param bool $newsletter
     * @return void
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * Returns the boolean state of newsletter
     *
     * @return bool
     */
    public function isNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Returns the weight
     *
     * @return int $weight
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Sets the weight
     *
     * @param int $weight
     * @return void
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * Returns the timeslot
     *
     * @return \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot
     */
    public function getTimeslot()
    {
        return $this->timeslot;
    }

    /**
     * Sets the timeslot
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot
     * @return void
     */
    public function setTimeslot(\Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot)
    {
        $this->timeslot = $timeslot;
    }

    /**
     * Returns the calendar
     *
     * @return \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Sets the calendar
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @return void
     */
    public function setCalendar(\Blueways\BwBookingmanager\Domain\Model\Calendar $calendar)
    {
        $this->calendar = $calendar;
    }
}
