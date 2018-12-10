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
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

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

        $this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $this->entryRepository = $this->objectManager->get('Blueways\\BwBookingmanager\\Domain\\Repository\\EntryRepository');

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
        $defaults = $this->getDefaultEmailSettings();

        $this->templateView->assignMultiple([
            'emailTemplates' => $emailTemplates,
            'entry' => $entry,
            'sendMailActionUri' => $sendMailActionUri,
            'defaults' => $defaults
        ]);
        $this->templateView->setTemplate('Administration/SendMailWizard');
        $content = $this->templateView->render();
        $response->getBody()->write($content);

        return $response;
    }

    /**
     * read typoscript for email settings
     *
     * @TODO: move to email helper utility
     * @return array
     */
    protected function getDefaultEmailSettings()
    {
        $defaults = array(
            'senderAddress' => $this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['settings.']['email.']['senderAddress'],
            'senderName' => $this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['settings.']['email.']['senderName'],
            'replytoAddress' => $this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['settings.']['email.']['replytoAddress'] ? $this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['settings.']['email.']['replytoAddress'] : $this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['settings.']['email.']['senderAddress'],
            'subject' => $this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['settings.']['email.']['subject'],
            'emailTemplate' => $this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['settings.']['email.']['template'],
            'showUid' => $this->typoscript['plugin.']['tx_bwbookingmanager_pi1.']['settings.']['email.']['showUid'] ?? null,
            'recipientAddress' => '',
            'recipientName' => '',
        );
        return $defaults;
    }

    /**
     * @param \TYPO3\CMS\Core\Http\ServerRequest $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function emailpreviewAction(\TYPO3\CMS\Core\Http\ServerRequest $request, ResponseInterface $response)
    {
        $queryParams = json_decode($request->getQueryParams()['arguments'], true);
        $emailSettings = $this->getDefaultEmailSettings();

        $entry = $this->entryRepository->findByUid($queryParams['entry']);
        $this->templateView->setTemplate('Email/' . $queryParams['emailTemplate']);
        $this->templateView->assign('entry', $entry);
        $this->templateView->assign('showUid', $emailSettings['showUid']);
        $html = $this->templateView->render();

        // hijack links and replace frontend links
        $html = $this->replaceInternalLinks($html);

        // extract marker and replace html with overrides from params
        $marker = $this->getMarkerInHtml($html);
        $markerContent = $this->getMarkerContentInHtml($html, $marker);

        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            if (isset($params['markerOverrides']) && sizeof($params['markerOverrides'])) {
                $html = $this->overrideMarkerContentInHtml($html, $marker, $params['markerOverrides']);
            }
        }

        // encode for display inside <iframe src="...">
        function encodeURIComponent($str)
        {
            $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
            return strtr(rawurlencode($str), $revert);
        }

        $src = 'data:text/html;charset=utf-8,' . encodeURIComponent($html);

        // build and encode response
        $content = json_encode(array(
            'src' => $src,
            'marker' => $marker,
            'markerContent' => $markerContent
        ));

        $response->getBody()->write($content);

        return $response;
    }

    /**
     * @param $html
     * @param $marker
     * @param $overrides
     * @return mixed
     */
    protected function overrideMarkerContentInHtml($html, $marker, $overrides)
    {
        // abbort if no overrides
        if (!$overrides || !sizeof($overrides)) {
            return $html;
        }

        // checks that there are no overrides for marker that dont exist
        $validOverrides = array_intersect($marker, array_keys($overrides));

        foreach ($validOverrides as $overrideName) {
            // abbort if no override content
            if (!$overrides[$overrideName]) {
                continue;
            }

            // replace everything from marker start to marker end with override content
            $regex = '/<!--\s+###' . $overrideName . '###\s+-->[\s\S]*<!--\s+###' . $overrideName . '###\s+-->/';
            $html = preg_replace($regex, $overrides[$overrideName], $html);
        }

        return $html;
    }

    /**
     * @param $html
     * @param $marker
     * @return array
     */
    protected function getMarkerContentInHtml($html, $marker)
    {
        $content = [];
        foreach ($marker as $m) {
            preg_match('/(<!--\s+###' . $m . '###\s+-->)([\s\S]*)(<!--\s+###' . $m . '###\s+-->)/', $html, $result);
            $content[] = array(
                'name' => $m,
                'content' => $result[2]
            );
        }
        return $content;
    }

    /**
     * @param $html
     * @return array
     */
    protected function getMarkerInHtml($html)
    {
        preg_match_all('/(<!--\s+###)([\w\d]\w+)(###\s+-->)/', $html, $foundMarker);

        // abort if no marker were found
        if (!sizeof($foundMarker[2])) {
            return [];
        }

        // ensure that two markers were found
        $markerOccurrences = array_count_values($foundMarker[2]);
        $markerOccurrences = array_filter($markerOccurrences, function ($occurrences) {
            return $occurrences === 2 ? true : false;
        });

        return array_keys($markerOccurrences);
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
        $uriArguments['signature'] = \TYPO3\CMS\Core\Utility\GeneralUtility::hmac(
            $uriArguments['arguments'],
            $routeName
        );

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
        $uriArguments['arguments'] = json_encode([]);
        $uriArguments['signature'] = \TYPO3\CMS\Core\Utility\GeneralUtility::hmac(
            $uriArguments['arguments'],
            $routeName
        );
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

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendMailAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $content = array(
            'status' => 'WARNING',
            'message' => [
                'headline' => 'Function not implemented yet.',
                'text' => 'Please contact the webmaster.'
            ]
        );

        if ($request->getMethod() !== 'POST') {
            return $response->withStatus(405, 'Method not allowed');
        }

        $params = $request->getParsedBody();

        $entryUid = $params['entryUid'] ?? false;

        if (!$entryUid) {
            return $response->withStatus(400, 'Form error');
        }

        $mailSettings = $this->getDefaultEmailSettings();

        // override defaults with POST parameter
        array_walk($mailSettings, function (&$value, $key, $params) {
            if (isset($params[$key]) && $params[$key] && $params[$key] !== "") {
                $value = $params[$key];
            }
        }, $params);

        // check that all params are collected and valid
        // @TODO: implement check

        // get html template
        $entry = $this->entryRepository->findByUid($entryUid);
        $this->templateView->setTemplate('Email/' . $mailSettings['emailTemplate']);
        $this->templateView->assign('entry', $entry);
        $this->templateView->assign('showUid', $mailSettings['showUid']);
        $html = $this->templateView->render();

        // check for overrides in POST and override html
        if (isset($params['markerOverrides']) && sizeof($params['markerOverrides'])) {
            $marker = $this->getMarkerInHtml($html);
            $html = $this->overrideMarkerContentInHtml($html, $marker, $params['markerOverrides']);
        }

        // actual send
        $message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $message->setTo($mailSettings['recipientAddress'], $mailSettings['recipientName'] ?? null)
            ->setFrom($mailSettings['senderAddress'], $mailSettings['senderName'] ?? null)
            ->setSubject($mailSettings['subject'])
            ->setBody($html, 'text/html');

        if ($mailSettings['senderAddress'] !== $mailSettings['replytoAddress']) {
            $message->setReplyTo($mailSettings['replytoAddress']);
        }

        $sendSuccess = $message->send();

        // sending successfull?
        if (!$sendSuccess) {
            // @TODO: return error
        }

        $content = array(
            'status' => 'OK',
            'message' => [
                'headline' => 'E-Mail send',
                'text' => 'Mail successfully send.'
            ]
        );
        $response->getBody()->write(json_encode($content));
        return $response;
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

    /**
     * @param $html
     * @return string
     */
    protected function replaceInternalLinks($html)
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        // find all links
        $re = '/<\s*a(\s+.*?>|>).*?<\s*/\s*a\s*>/';
        preg_match($re, $html, $links);

        foreach ($links as $link) {
            // abbort if not an internal link
            $link = rawurldecode($link);
            if (strpos($link, '/typo3/index.php?M=') === false) {
                continue;
            }

            // extract parameters
            preg_match('/(tx_bwbookingmanager_pi1\[)([\w]+)(\]=)([\w]+)(&|")/', $link, $linkArguments);

            // create new link
            $pageUid = 1;
            $getAction = '';
            $getController = '';
            $getArgs = [];
            foreach ($linkArguments as $arg) {
                if ($arg[2] === 'controller') {
                    $getController = $arg[4];
                } elseif ($arg[2] == 'action') {
                    $getAction = $arg[4];
                } else {
                    $getArgs[] = [$arg[2] => $arg[4]];
                }
            }
            $uri = $uriBuilder->reset()
                ->setTargetPageUid($pageUid)
                ->setCreateAbsoluteUri(true)
                ->uriFor($getAction, $getArgs, $getController, 'bwbookingmanager', 'pi1');

            $html = str_replace($link, $uri, $html);
        }

        return $html;
    }
}
