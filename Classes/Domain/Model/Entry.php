<?php

namespace Blueways\BwBookingmanager\Domain\Model;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
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
     * @validate NotEmpty, DateTime
     */
    protected $startDate;

    /**
     * endDate
     *
     * @var \DateTime
     * @validate NotEmpty, DateTime
     */
    protected $endDate;

    /**
     * name
     *
     * @var string
     * @validate NotEmpty, StringLengthValidator(minimum=3, maximum=50)
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
     * @validate NotEmpty, EmailAddress
     */
    protected $email = '';

    /**
     * newsletter
     *
     * @var bool
     */
    protected $newsletter = false;

    /**
     * confirmed
     *
     * @var bool
     */
    protected $confirmed = false;

    /**
     * special1
     *
     * @var bool
     */
    protected $special1 = false;

    /**
     * special2
     *
     * @var bool
     */
    protected $special2 = false;

    /**
     * weight
     *
     * @var int
     * @validate Integer
     */
    protected $weight = 1;

    /**
     * calendar
     *
     * @lazy
     * @var \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @validate NotEmpty
     */
    protected $calendar = null;

    /**
     * timeslot
     *
     * @lazy
     * @var \Blueways\BwBookingmanager\Domain\Model\Timeslot
     */
    protected $timeslot = null;

    /**
     * token
     *
     * @var string
     */
    protected $token = '';

    /**
     * @var int
     */
    protected $crdate;

    /**
     * @lazy
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $feUser;

    /**
     * __construct
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return void
     */
    public function __construct(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar = null,
        \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot = null,
        \DateTime $startDate = null,
        \DateTime $endDate = null
    ) {
        if ($calendar) {
            $this->setCalendar($calendar);
        }
        if ($timeslot) {
            $this->setTimeslot($timeslot);
        }
        if ($startDate) {
            $this->setStartDate($startDate);
        }
        if ($endDate) {
            $this->setEndDate($endDate);
        }
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser|null
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser
     */
    public function setFeUser(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser)
    {
        $this->feUser = $feUser;
    }

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
     * Returns the token
     *
     * @return string $token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the token
     *
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
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
     * Returns the boolean state of newsletter
     *
     * @return bool
     */
    public function isNewsletter()
    {
        return $this->newsletter;
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
     * Returns the confirmed
     *
     * @return bool $confirmed
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Returns the boolean state of confirmed
     *
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Sets the confirmed
     *
     * @param bool $confirmed
     * @return void
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }

    /**
     * Returns the boolean state of special1
     *
     * @return bool
     */
    public function isSpecial1()
    {
        return $this->special1;
    }

    /**
     * Returns the special1
     *
     * @return bool $special1
     */
    public function getSpecial1()
    {
        return $this->special1;
    }

    /**
     * Sets the special1
     *
     * @param bool $special1
     * @return void
     */
    public function setSpecial1($special1)
    {
        $this->special1 = $special1;
    }

    /**
     * Returns the boolean state of special2
     *
     * @return bool
     */
    public function isSpecial2()
    {
        return $this->special2;
    }

    /**
     * Returns the special1
     *
     * @return bool $special1
     */
    public function getSpecial2()
    {
        return $this->special2;
    }

    /**
     * Sets the special2
     *
     * @param bool $special2
     * @return void
     */
    public function setSpecial2($special2)
    {
        $this->special2 = $special2;
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

    public function generateToken()
    {
        $this->token = bin2hex(random_bytes(64));
    }

    public function isValidToken($token)
    {
        return $token && $this->token === $token ? true : false;
    }

    public function getDisplayStartDate()
    {
        $date = $this->startDate;
        if ($date) {
            $date->setTimezone(new \DateTimeZone('Europe/Berlin'));
        }
        return $date;
    }

    public function getDisplayEndDate()
    {
        $date = $this->endDate;
        if ($date) {
            $date->setTimezone(new \DateTimeZone('Europe/Berlin'));
        }
        return $date;
    }

    /**
     * @return int
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    public function getApiOutput()
    {
        return [
            'uid' => $this->uid,
            'start' => $this->startDate->format('c'),
            'end' => $this->endDate->format('c')
        ];
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser
     */
    public function mergeWithFeUser($feUser)
    {
        $this->setFeUser($feUser);

        if ($feUser->getEmail()) {
            $this->setEmail($feUser->getEmail());
        }
        if ($feUser->getName()) {
            $this->setName($feUser->getName());
        }
        if ($feUser->getFirstName()) {
            $this->setPrename($feUser->getFirstName());
        }
        if ($feUser->getLastName()) {
            $this->setName($feUser->getLastName());
        }
        if ($feUser->getAddress()) {
            $this->setStreet($feUser->getAddress());
        }
        if ($feUser->getZip()) {
            $this->setZip($feUser->getZip());
        }
        if ($feUser->getTelephone()) {
            $this->setPhone($feUser->getTelephone());
        }
    }
}
