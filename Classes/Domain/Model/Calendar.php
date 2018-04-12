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
 * Calendar
 */
class Calendar extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * timeslots
     *
     * @var \Blueways\BwBookingmanager\Domain\Model\Timeslot
     */
    protected $timeslots = null;

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
     * Returns the timeslots
     *
     * @return \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslots
     */
    public function getTimeslots()
    {
        return $this->timeslots;
    }

    /**
     * Sets the timeslots
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslots
     * @return void
     */
    public function setTimeslots(\Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslots)
    {
        $this->timeslots = $timeslots;
    }
}
