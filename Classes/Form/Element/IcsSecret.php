<?php

namespace Blueways\BwBookingmanager\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class IcsSecret extends AbstractFormElement
{

    public function render(): array
    {
        $name = 'data[tx_bwbookingmanager_domain_model_ics][' . $this->data['vanillaUid'] . '][secret]';
        $value = $this->data['databaseRow']['secret'];
        /** @var \TYPO3\CMS\Core\Site\Entity\Site $site */
        $site = $this->data['site'];
        $url = $site->getBase();
        $url .= '/ics/' . $this->data['vanillaUid'];
        $url .= $this->data['databaseRow']['secret'] ? '/' : '';
        $url .= $this->data['databaseRow']['secret'] . '.ics';

        $urlStart = $site->getBase() . '/ics/' . $this->data['vanillaUid'];

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $languageService = $objectManager->get(LanguageService::class);
        $buttonTextCopy = $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:button.copy');
        $buttonTextRefresh = $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:button.refresh');

        $result['html'] = '
            <div class="formengine-field-item t3js-formengine-field-item">
                <div class="form-control-wrap" id="t3js-form-field-secret">
                <div style="display: grid; grid-template-columns: 1fr 1fr">

                <div class="input-group" style="display: grid; grid-template-columns: auto min-content">

                <input type="text" data-url-start="' . $urlStart . '" value="' . $url . '" id="inputUrl" class="form-control t3js-form-field" readonly />

                <button id="copyButton" class="btn btn-default" type="button">
                    <span class="icon icon-size-small icon-state-default">
                        <span class="icon-markup">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><g class="icon-color"><path d="M6.585 2H3.51a.502.502 0 0 0-.51.494v12.012c0 .268.228.494.51.494h6.987-1.994c-.652 0-1.208-.42-1.416-1H4V3h1v.495c0 .291.22.505.491.505h5.018A.503.503 0 0 0 11 3.495V3h1v3h1V2.494A.505.505 0 0 0 12.49 2H9.415a1.5 1.5 0 0 0-2.83 0zM8 3.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5zm0 4.253v5.994c0 .27.225.503.503.503h5.994c.27 0 .503-.225.503-.503V7.503A.508.508 0 0 0 14.497 7H8.503A.508.508 0 0 0 8 7.503zM9 8h5v5H9V8zm1 1h3v1h-3V9zm0 2h3v1h-3v-1z"/></g></svg>
                        </span>
                    </span>
                    ' . $buttonTextCopy . '
                </button>

                </div>

                <div class="input-group" style="display: grid; grid-template-columns: auto min-content; padding-left: 20px;">

                    <div class="form-control-clearable">
                        <input class="form-control t3js-form-field" id="inputSecret" value="' . $value . '" name="' . $name . '">
                        <button id="resetButton" type="button" class="close" tabindex="-1" aria-hidden="true"><span class="t3js-icon icon icon-size-small icon-state-default icon-actions-close" data-identifier="actions-close"><span class="icon-markup"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M11.9 5.5L9.4 8l2.5 2.5c.2.2.2.5 0 .7l-.7.7c-.2.2-.5.2-.7 0L8 9.4l-2.5 2.5c-.2.2-.5.2-.7 0l-.7-.7c-.2-.2-.2-.5 0-.7L6.6 8 4.1 5.5c-.2-.2-.2-.5 0-.7l.7-.7c.2-.2.5-.2.7 0L8 6.6l2.5-2.5c.2-.2.5-.2.7 0l.7.7c.2.2.2.5 0 .7z" class="icon-color"></path></svg></span></span></button>
                    </div>

                    <button class="btn btn-default" type="button" id="refreshButton">
                        <span class="t3js-icon icon icon-size-small icon-state-default" data-identifier="actions-refresh">
                            <span class="icon-markup">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><g class="icon-color"><path d="M15.549 8H14c0-3.31-2.69-6-6-6a6 6 0 0 0-3.06.84l.61.97c.72-.42 1.55-.68 2.45-.68 2.68 0 4.87 2.18 4.87 4.87h-1.419a.25.25 0 0 0-.212.383l2.049 3.278a.25.25 0 0 0 .424 0l2.049-3.278A.25.25 0 0 0 15.549 8zM10.37 12.23c-.7.4-1.5.64-2.37.64-2.68 0-4.87-2.18-4.87-4.87h1.419a.25.25 0 0 0 .212-.383L2.712 4.339a.25.25 0 0 0-.424 0L.239 7.617A.25.25 0 0 0 .451 8H2c0 3.31 2.69 6 6 6 1.09 0 2.1-.29 2.98-.8l-.61-.97z"/></g></svg>
                            </span>
                        </span>
                        ' . $buttonTextRefresh . '
                    </button>


                </div>
                </div>
                </div>
                </div>
        ';

        $result['requireJsModules'][] = 'TYPO3/CMS/BwBookingmanager/IcsSecret';

        return $result;
    }
}
