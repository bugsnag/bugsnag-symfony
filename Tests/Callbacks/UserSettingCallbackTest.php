<?php

namespace Bugsnag\BugsnagBundle\Tests\Callbacks;

use Bugsnag\BugsnagBundle\Callbacks\UserSettingCallback;
use Bugsnag\Report;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\User;

class UserSettingCallbackTest extends TestCase
{
    public function testUserNotSetWhenDisabled()
    {
        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)
            ->getMock();

        $authorizationChecker = $this->getMockBuilder(AuthorizationCheckerInterface::class)
            ->getMock();

        $reportMock = $this->getBugsnagReportMock();
        $reportMock
            ->expects($this->never())
            ->method('setUser');

        $callback = new UserSettingCallback($tokenStorageMock, $authorizationChecker, false);
        $callback->registerCallback($reportMock);
    }

    public function testUserNotSetWhenServicesNotPassed()
    {
        $reportMock = $this->getBugsnagReportMock();
        $reportMock
            ->expects($this->never())
            ->method('setUser');

        $callback = new UserSettingCallback(null, null, true);
        $callback->registerCallback($reportMock);
    }

    public function testUserIsSetWhenLoggedIn()
    {
        $user = new User('example', 'example');

        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->getMock();

        $tokenMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)
            ->getMock();

        $tokenStorageMock
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($tokenMock);

        $authorizationChecker = $this->getMockBuilder(AuthorizationCheckerInterface::class)
            ->getMock();

        $authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with('IS_AUTHENTICATED_REMEMBERED')
            ->willReturn(true);

        $reportMock = $this->getBugsnagReportMock();
        $reportMock
            ->expects($this->once())
            ->method('setUser')
            ->with([
                'id' => $user->getUsername(),
            ]);

        $callback = new UserSettingCallback($tokenStorageMock, $authorizationChecker, true);
        $callback->registerCallback($reportMock);
    }

    private function getBugsnagReportMock()
    {
        return $this->getMockBuilder(Report::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
