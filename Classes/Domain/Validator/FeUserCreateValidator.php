<?php

namespace Blueways\BwBookingmanager\Domain\Validator;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
/**
 * Class FeUserCreateValidator
 *
 * @package Blueways\BwBookingmanager\Domain\Validator
 */
class FeUserCreateValidator extends AbstractValidator
{

    /**
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @param mixed $user
     * @return bool
     */
    public function isValid($user)
    {
        // check for password
        if (!$user->getPassword()) {
            $this->addError('Password required', 1581677979);
        }

        // validate username
        $this->checkForUniqueUsername($user);

        if (sizeof($this->result->getErrors())) {
            return false;
        }
        return true;
    }

    private function checkForUniqueUsername($user)
    {
        $query = $this->frontendUserRepository->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->equals('username', $user->getUsername())
        );
        $users = $query->execute()->toArray();
        if (count($users)) {
            $this->addError('User already exists', 1581619398);
        }
    }

    public function injectFrontendUserRepository(
        FrontendUserRepository $frontendUserRepository
    ) {
        $this->frontendUserRepository = $frontendUserRepository;
    }
}
