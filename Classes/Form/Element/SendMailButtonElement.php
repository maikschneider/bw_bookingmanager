<?php

namespace Blueways\BwBookingmanager\Form\Element;

class SendMailButtonElement extends \TYPO3\CMS\Backend\Form\Element\AbstractFormElement
{

    /**
     * Renders the button element
     *
     * @return array
     */
    public function render()
    {
        $resultArray = $this->initializeResultArray();

        $resultArray['requireJsModules'][] = [
            'TYPO3/CMS/BwBookingmanager/SendMailWizard' => 'function(SendMailWizard){top.require(["jquery-ui/draggable", "jquery-ui/resizable"], function() {}); }',
        ];

        $wizardUri = $this->getWizardUri();

        $modalTitle = 'E-Mail Preview';
        $modalCancelButtonText = 'Cancel';
        $modalSendButtonText = 'Send';

        $html = '';
        $html .= '<div class="formengine-field-item t3js-formengine-field-item" style="margin-top: -20px;">';
        $html .= '<label class="t3js-formengine-label" for="sendmailButton">Confirmation mail</label>';
        $html .= '<div class="form-wizards-wrap">';
        $html .= '<div class="form-wizards-element">';
        $html .= '<div class="form-control-wrap">';
        $html .= '<button 
            id="sendMailButton"
            class="btn btn-default t3js-sendmail-trigger"
            data-wizard-uri="' . $wizardUri . '" 
            data-modal-title="' . $modalTitle . '" 
            data-modal-send-button-text="' . $modalSendButtonText . '" 
            data-modal-cancel-button-text="' . $modalCancelButtonText . '">
			  <span class="t3-icon fa fa-envelope-o"></span> Send new E-Mail</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $resultArray['html'] = $html;

        return $resultArray;
    }

    protected function getWizardUri()
    {
        $routeName = 'ajax_wizard_sendbookingmail';

        $uriArguments['arguments'] = json_encode([
            'entry' => $this->data['databaseRow']['uid'],
            'emailTemplate' => ''
        ]);
        $uriArguments['signature'] = \TYPO3\CMS\Core\Utility\GeneralUtility::hmac($uriArguments['arguments'],
            $routeName);

        $uriBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);

        return (string)$uriBuilder->buildUriFromRoute($routeName, $uriArguments);
    }

}
