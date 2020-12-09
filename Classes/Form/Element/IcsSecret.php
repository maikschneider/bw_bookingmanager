<?php

namespace Blueways\BwBookingmanager\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;

class IcsSecret extends AbstractFormElement
{

    public function render(): array
    {
        // Custom TCA properties and other data can be found in $this->data, for example the above
        // parameters are available in $this->data['parameterArray']['fieldConf']['config']['parameters']
        $result = $this->initializeResultArray();
        $result['html'] = 'my map content';
        return $result;
    }
}
