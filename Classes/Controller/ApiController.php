<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class ApiController extends ActionController
{

    /**
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     * @inject
     */
    protected $calendarRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
     * @inject
     */
    protected $timeslotRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    public function calendarListAction()
    {
        $calendars = $this->calendarRepository->findAllIgnorePid();
        $uris = [];

        foreach ($calendars as $key => $calendar) {
            $uris[$key] = $this->uriBuilder
                ->setCreateAbsoluteUri(true)
                ->setTargetPageType(555)
                ->uriFor('calendarShow', ['calendar' => $calendar->getUid()], 'Api', 'BwBookingmanager', 'Pi1');
        }

        $this->view->assign('calendars', $calendars);
        $this->view->assign('uris', $uris);

        $this->view->setVariablesToRender(array('calendars', 'uris'));
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function calendarShowAction(Calendar $calendar)
    {
        if (!$calendar) {
            $this->throwStatus(404, 'Calendar not found');
        }

        $startDate = new \DateTime('now');
        $startDate->setTime(0, 0, 0);
        $day = $this->request->hasArgument('day') ? $this->request->getArgument('day') : null;
        $month = $this->request->hasArgument('month') ? $this->request->getArgument('month') : null;
        $year = $this->request->hasArgument('year') ? $this->request->getArgument('year') : null;
        if ($day && $month && $year) {
            $startDate = $startDate->createFromFormat('j-n-Y H:i:s', $day . '-' . $month . '-' . $year . ' 00:00:00');
        }
        $dateConf = new DateConf((int)$this->settings['dateRange'], $startDate);

        // query calendar, entries, timeslots
        /** @var Calendar $calendar */
        $entries = $this->entryRepository->findInRange($calendar, $dateConf);
        $timeslots = $this->timeslotRepository->findInRange($calendar, $dateConf);

        // build render configuration
        $calendarConfiguration = new \Blueways\BwBookingmanager\Helper\RenderConfiguration($dateConf, $calendar);
        $calendarConfiguration->setTimeslots($timeslots);
        $calendarConfiguration->setEntries($entries);
        $configuration = $calendarConfiguration->getRenderConfiguration();

        $this->view->assignMultiple([
            'configuration' => $configuration,
        ]);

        $this->view->setVariablesToRender(array('configuration'));
    }

    public function initializeEntryCreateAction()
    {
        if ($this->arguments->hasArgument('newEntry')) {
            // allow creation of Entry
            $propertyMappingConfiguration = $this->arguments->getArgument('newEntry')->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowAllProperties();
            $propertyMappingConfiguration->setTypeConverterOption('TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter',
                \TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
                true);

            // convert timestamps
            $propertyMappingConfiguration->forProperty('startDate')->setTypeConverterOption(
                'TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'U'
            );
            $propertyMappingConfiguration->forProperty('endDate')->setTypeConverterOption(
                'TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'U'
            );

            // set new validator
            $newEntry = $this->request->getArgument('newEntry');
            $calendar = $this->calendarRepository->findByIdentifier($newEntry['calendar']);
            $entityClass = $calendar::ENTRY_TYPE_CLASSNAME;

            $validatorResolver = $this->objectManager->get(\TYPO3\CMS\Extbase\Validation\ValidatorResolver::class);
            $validatorConjunction = $validatorResolver->getBaseValidatorConjunction($entityClass);
            $entryValidator = $validatorResolver->createValidator('\Blueways\BwBookingmanager\Domain\Validator\EntryCreateValidator');
            $validatorConjunction->addValidator($entryValidator);

            $this->arguments->getArgument('newEntry')->setValidator($validatorConjunction);

            $newEntry = $this->arguments['newEntry'];
            $newEntry->setDataType($calendar::ENTRY_TYPE_CLASSNAME);

        }
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $newEntry
     * @Extbase\IgnoreValidation("newEntry")
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function entryCreateAction($newEntry)
    {
        $newEntry->generateToken();
        // override PID (just in case the storage PID differs from current calendar)
        $newEntry->setPid($newEntry->getCalendar()->getPid());
        $this->entryRepository->add($newEntry);

        // persist by hand to get uid field and make redirect possible
        $persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $persistenceManager->persistAll();

        $this->view->assign('entry', $newEntry);
        $this->view->setVariablesToRender(array('entry'));
    }

    public function errorAction()
    {
        if ($this->request->getControllerActionName() === "entryCreate") {
            $errors = $this->arguments->validate()->forProperty('newEntry')->getFlattenedErrors();

            $errors = array_map(function($error){
                return $error[0]->getMessage();
            }, $errors);

            $content = [
                'errors:' => $errors
            ];

            $this->throwStatus(406, 'Validation failed', json_encode($content));

        }
    }
}
