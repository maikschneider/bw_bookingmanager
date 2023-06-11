<?php

namespace Blueways\BwBookingmanager\Task;

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class RecycleEntriesAdditionalFieldProvider extends AbstractAdditionalFieldProvider
{
    /**
     * @var int Default number of days
     */
    protected $defaultNumberOfDays = 30;

    /**
     * Gets additional fields to render in the form to add/edit a task
     *
     * @param array $taskInfo Values of the fields from the add/edit task form
     * @param AbstractTask $task The task object being edited. Null when adding a task!
     * @param SchedulerModuleController $schedulerModule Reference to the scheduler backend module
     * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
     */
    public function getAdditionalFields(
        array &$taskInfo,
        $task,
        SchedulerModuleController $schedulerModule
    ) {
        // Initialize selected fields
        if (!isset($taskInfo['scheduler_recycleEntries_numberOfDays'])) {
            $taskInfo['scheduler_recycleEntries_numberOfDays'] = $this->defaultNumberOfDays;
            if ((string)$schedulerModule->getCurrentAction() === 'edit') {
                $taskInfo['scheduler_recycleEntries_numberOfDays'] = $task->numberOfDays;
            }
        }

        $fieldName = 'tx_scheduler[scheduler_recycleEntries_numberOfDays]';
        $fieldId = 'scheduler_recycleEntries_numberOfDays';
        $fieldValue = (int)$taskInfo['scheduler_recycleEntries_numberOfDays'];
        $fieldHtml = '<input class="form-control" type="text" name="' . $fieldName . '" id="' . $fieldId . '" value="' . htmlspecialchars($fieldValue) . '">';
        $additionalFields[$fieldId] = [
            'code' => $fieldHtml,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:recyclertask.numberOfDays',
            'cshKey' => '_MOD_system_txschedulerM1',
            'cshLabel' => $fieldId,
        ];
        return $additionalFields;
    }

    /**
     * Validates the additional fields' values
     *
     * @param array $submittedData An array containing the data submitted by the add/edit task form
     * @param SchedulerModuleController $schedulerModule Reference to the scheduler backend module
     * @return bool TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
     */
    public function validateAdditionalFields(
        array &$submittedData,
        SchedulerModuleController $schedulerModule
    ) {
        $result = true;
        // Check if number of days is indeed a number and greater or equals to 0
        // If not, fail validation and issue error message
        if (!is_numeric($submittedData['scheduler_recycleEntries_numberOfDays']) || (int)$submittedData['scheduler_recycleEntries_numberOfDays'] < 0) {
            $result = false;
            $this->addMessage($GLOBALS['LANG']->sL('LLL:EXT:scheduler/Resources/Private/Language/locallang.xlf:msg.invalidNumberOfDays'), AbstractMessage::ERROR);
        }
        return $result;
    }

    /**
     * Takes care of saving the additional fields' values in the task's object
     *
     * @param array $submittedData An array containing the data submitted by the add/edit task form
     * @param AbstractTask $task Reference to the scheduler backend module
     */
    public function saveAdditionalFields(array $submittedData, AbstractTask $task)
    {
        $task->numberOfDays = (int)$submittedData['scheduler_recycleEntries_numberOfDays'];
    }
}
