<?php

namespace Bugsnag\BugsnagBundle\Callbacks;

use Bugsnag\Report;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserSettingCallback
{
    /**
     * The token resolver.
     *
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface|null
     */
    protected $tokens;

    /**
     * The auth checker.
     *
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface|null
     */
    protected $checker;

    /**
     * @var bool
     */
    protected $setUser;

    /**
     * @param null|\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokens
     * @param null|\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface        $checker
     * @param bool                                                                                     $setUser
     */
    public function __construct(
        TokenStorageInterface $tokens = null,
        AuthorizationCheckerInterface $checker = null,
        $setUser = true
    ) {
        $this->tokens = $tokens;
        $this->checker = $checker;
        $this->setUser = $setUser;
    }

    /**
     * @param \Bugsnag\Report $report
     *
     * @return void
     */
    public function registerCallback(Report $report)
    {
        // If told to not set the user, or the security services were not passed in
        // (not registered in the container), then exit early
        if (!$this->setUser || is_null($this->tokens) || is_null($this->checker)) {
            return;
        }

        $token = $this->tokens->getToken();

        if (!$token || !$this->checker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return;
        }

        $user = $token->getUser();

        if ($user instanceof UserInterface) {
            $bugsnagUser = ['id' => $user->getUsername()];
        } else {
            $bugsnagUser = ['id' => (string) $user];
        }

        $report->setUser($bugsnagUser);
    }
}
