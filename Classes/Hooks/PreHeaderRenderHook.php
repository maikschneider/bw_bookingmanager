<?php

namespace Blueways\BwBookingmanager\Hooks;

use TYPO3\CMS\Core\Page\PageRenderer;
class PreHeaderRenderHook
{
    public function addFullCalendarJs($parameter)
    {
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = $parameter['pageRenderer'];
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/BwBookingmanager/BackendModalCalendar', 'function(BackendModalCalendar){

        window.BackendModalCalendar = new BackendModalCalendar();

        }');
    }
}
