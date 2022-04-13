<?php

declare(strict_types=1);

namespace Blueways\BwBookingmanager\Controller\Backend;

use Blueways\BwBookingmanager\Domain\Model\Dto\AdministrationDemand;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EntryListModuleController extends AbstractModuleController
{

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->initializeExtbaseController('entryList', $request);

        $hideForm = true;
        $queryParams = $this->request->getQueryParams();
        $demand = GeneralUtility::makeInstance(AdministrationDemand::class);
        // override default demand values with values from GET request
        if (is_array($queryParams['demand'])) {
            $hideForm = false;
            foreach ($queryParams['demand'] as $key => $value) {
                if (property_exists(AdministrationDemand::class, $key)) {
                    $getter = 'set' . ucfirst($key);
                    $demand->$getter($value);
                }
            }
        }

        $calendars = $this->calendarRepository->findAll();
        $calendar = $calendars && $calendars->count() ? $calendars->getFirst() : [];

        // save selected route
        $moduleDataIdentifier = 'bwbookingmanager/selectedRoute-' . $this->pid;
        $GLOBALS['BE_USER']->pushModuleData($moduleDataIdentifier, 0);

        $this->view->assign('hideForm', $hideForm);
        $this->view->assign('page', $this->pid);
        $this->view->assign('demand', $demand);
        $this->view->assign('settings', $this->settings);
        $this->view->assign('calendar', $calendar);
        $this->view->assign('calendars', $calendars);

        $this->moduleTemplate->setContent($this->view->render());
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
        $response->getBody()->write($this->moduleTemplate->renderContent());
        return $response;
    }
}