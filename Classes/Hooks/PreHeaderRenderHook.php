<?php

namespace Blueways\BwBookingmanager\Hooks;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PreHeaderRenderHook
{
    public function addFullCalendarJs(): void
    {
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/BwBookingmanager/BackendModalCalendar', 'function(BackendModalCalendar){
            window.BackendModalCalendar = new BackendModalCalendar();
        }');
    }
}
