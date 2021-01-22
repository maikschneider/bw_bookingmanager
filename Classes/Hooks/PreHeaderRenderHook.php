<?php

namespace Blueways\BwBookingmanager\Hooks;

class PreHeaderRenderHook
{
    public function addFullCalendarJs($parameter)
    {
        /** @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
        $pageRenderer = $parameter['pageRenderer'];
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/BwBookingmanager/BackendModalCalendar', 'function(BackendModalCalendar){

        window.BackendModalCalendar = new BackendModalCalendar();

        }');
    }
}
