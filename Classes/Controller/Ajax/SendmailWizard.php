<?php

namespace Blueways\BwBookingmanager\Controller\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class SendmailWizard extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var StandaloneView
     */
    private $templateView;

    /**
     * CalendarRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     */
    protected $entryRepository = null;

    /**
     * @var array
     */
    protected $queryParams = null;

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * @var array
     */
    protected $typoscript;

    /**
     * @var \Blueways\BwBookingmanager\Helper\NotificationManager
     */
    protected $notificationManager;

    /**
     * SendmailWizard constructor.
     *
     * @param \TYPO3\CMS\Fluid\View\StandaloneView|null $templateView
     */
    public function __construct(StandaloneView $templateView = null)
    {
        parent::__construct();

        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $this->entryRepository = $objectManager->get('Blueways\\BwBookingmanager\\Domain\\Repository\\EntryRepository');

        $configurationManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $this->typoscript = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        if (!$templateView) {
            $templateView = GeneralUtility::makeInstance(StandaloneView::class);
            $templateView->setLayoutRootPaths($this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['view.']['layoutRootPaths.']);
            $templateView->setPartialRootPaths($this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['view.']['partialRootPaths.']);
            $templateView->setTemplateRootPaths($this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['view.']['templateRootPaths.']);
        }
        $this->templateView = $templateView;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function modalContentAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->queryParams = json_decode($request->getQueryParams()['arguments'], true);
        $entry = $this->entryRepository->findByUid($this->queryParams['entry']);
        $emailTemplates = $this->getEmailTemplateSelection();
        $sendMailActionUri = $this->getSendMailUri();

        $this->templateView->assignMultiple([
            'emailTemplates' => $emailTemplates,
            'entry' => $entry,
            'sendMailActionUri' => $sendMailActionUri
        ]);
        $this->templateView->setTemplate('Administration/SendMailWizard');
        $content = $this->templateView->render();
        $response->getBody()->write($content);

        return $response;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function emailpreviewAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $queryParams = json_decode($request->getQueryParams()['arguments'], true);

        $entry = $this->entryRepository->findByUid($queryParams['entry']);

        $this->templateView->setTemplate('Email/' . $queryParams['emailTemplate']);
        $this->templateView->assign('entry', $entry);
        $html = $this->templateView->render();
        function encodeURIComponent($str)
        {
            $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
            return strtr(rawurlencode($str), $revert);
        }

        $src = 'data:text/html;charset=utf-8,' . encodeURIComponent($html);
        $content = json_encode(array(
            'src' => $src
        ));
        $response->getBody()->write($content);

        return $response;
    }

    /**
     * @param string $emailTemplate
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    protected function getEmailPreviewUri($emailTemplate)
    {
        $routeName = 'ajax_emailpreview';

        $newQueryParams = $this->queryParams;
        $newQueryParams['emailTemplate'] = $emailTemplate;

        $uriArguments['arguments'] = json_encode($newQueryParams);
        $uriArguments['signature'] = \TYPO3\CMS\Core\Utility\GeneralUtility::hmac($uriArguments['arguments'],
            $routeName);

        $uriBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);

        return (string)$uriBuilder->buildUriFromRoute($routeName, $uriArguments);
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    protected function getSendMailUri()
    {
        $routeName = 'ajax_sendbookingmail';
        $uriArguments['signature'] = \TYPO3\CMS\Core\Utility\GeneralUtility::hmac('', $routeName);
        $uriBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute($routeName);
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    protected function getEmailTemplateSelection()
    {
        $pageTsConfig = BackendUtility::getPagesTSconfig(0);
        $emailTemplates = $pageTsConfig['TCEFORM.']['tt_content.']['pi_flexform.']['bwbookingmanager_pi1.']['email.']['settings.mail.template.']['addItems.'];
        $selection = [];
        foreach ($emailTemplates as $key => $emailTemplate) {
            $selection[] = array(
                'file' => $key,
                'name' => $this->getLanguageService()->sL($emailTemplate),
                'previewUri' => $this->getEmailPreviewUri($key)
            );
        }
        return $selection;
    }

    public function sendMailAction(ServerRequestInterface $request, ResponseInterface $response)
    {

        //$this->notificationManager = GeneralUtility::makeInstance('Blueways\\BwBookingmanager\\Helper\\NotificationManager', $entry);
    }

    /**
     * Returns the LanguageService
     *
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

}
