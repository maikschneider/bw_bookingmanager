<?php
namespace Blueways\BwBookingmanager\Domain\Model\Dto;

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
 * Blockslot
 */
class AdministrationDemand extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $recursive;

    /**
     * @var string
     */
    protected $sortingField = 'datetime';

    /**
     * @var string
     */
    protected $sortingDirection = 'desc';

    /**
     * @var string
     */
    protected $searchWord;

    /**
     * @var string
     */
    protected $startDate;

    /**
     * @var string
     */
    protected $endDate;

    /**
     * @var int
     */
    protected $hidden;

    public function __construct()
    {
        $now = new \DateTime('now');
        $this->startDate = $now->format('d.m.Y');
    }

    /**
     * @return string
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * @param $recursive
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }

    /**
     * @return string
     */
    public function getSortingField()
    {
        return $this->sortingField;
    }

    /**
     * @param $sortingField
     */
    public function setSortingField($sortingField)
    {
        $this->sortingField = $sortingField;
    }

    /**
     * @return string
     */
    public function getSortingDirection()
    {
        return $this->sortingDirection;
    }

    /**
     * @param $sortingDirection
     */
    public function setSortingDirection($sortingDirection)
    {
        $this->sortingDirection = $sortingDirection;
    }

    /**
     * @return string
     */
    public function getSearchWord()
    {
        return $this->searchWord;
    }

    /**
     * @param string $searchWord
     */
    public function setSearchWord($searchWord)
    {
        $this->searchWord = $searchWord;
    }

    /**
     * @return int
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * @param int $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

}
