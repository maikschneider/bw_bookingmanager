<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Blueways.BwBookingmanager',
            'Pi1',
            [
                'Calendar' => 'list, show',
                'Timeslot' => 'list, show',
                'Entry' => 'new, create, show'
            ],
            // non-cacheable actions
            [
                'Calendar' => '',
                'Timeslot' => '',
                'Entry' => 'create'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    bookingmanager {
                        icon = ' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('bw_bookingmanager') . 'Resources/Public/Icons/user_plugin_bookingmanager.svg
                        title = LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bw_bookingmanager_domain_model_bookingmanager
                        description = LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bw_bookingmanager_domain_model_bookingmanager.description
                        tt_content_defValues {
                            CType = list
                            list_type = bwbookingmanager_pi1
                        }
                    }
                }
                show = *
            }
       }
       <INCLUDE_TYPOSCRIPT: source="FILE:EXT:bw_bookingmanager/Configuration/TSconfig/TCEFORM.txt">'
    );
    }
);
