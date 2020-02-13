<?php

namespace Blueways\BwBookingmanager\Domain\Validator;

/**
 * Class FeUserCreateValidator
 *
 * @package Blueways\BwBookingmanager\Domain\Validator
 */
class FeUserCreateValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     *
     */
    protected $frontendUserRepository;

    /**
     * @param mixed $user
     * @return bool
     */
    public function isValid($user)
    {
        $users = $this->frontendUserRepository->findByUsername($user->getUsername());
        if (count($users)) {
            $this->addError('User already exists', 1581619398, [], 'title');
        }

        if (sizeof($this->result->getErrors())) {
            return false;
        }
        return true;
    }

    public function injectFrontendUserRepository(
        \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $frontendUserRepository
    ) {
        $this->frontendUserRepository = $frontendUserRepository;
    }
}
