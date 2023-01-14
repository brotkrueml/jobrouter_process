<?php

declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Tests\Unit\Command;

use Brotkrueml\JobRouterProcess\Command\StartCommand;
use Brotkrueml\JobRouterProcess\Domain\Entity\CountResult;
use Brotkrueml\JobRouterProcess\Extension;
use Brotkrueml\JobRouterProcess\Transfer\Starter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Locking\Exception\LockAcquireException;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Locking\LockingStrategyInterface;
use TYPO3\CMS\Core\Registry;

class StartCommandTest extends TestCase
{
    private CommandTester $commandTester;

    /**
     * @var LockingStrategyInterface&MockObject
     */
    private MockObject $lockerMock;

    /**
     * @var Starter&MockObject
     */
    private MockObject $starterMock;

    /**
     * @var MockObject&Registry
     */
    private MockObject $registryMock;

    protected function setUp(): void
    {
        $this->lockerMock = $this->createMock(LockingStrategyInterface::class);
        $lockFactoryStub = $this->createStub(LockFactory::class);
        $lockFactoryStub
            ->method('createLocker')
            ->willReturn($this->lockerMock);

        $this->starterMock = $this->createMock(Starter::class);
        $this->registryMock = $this->createMock(Registry::class);

        $command = new StartCommand($lockFactoryStub, $this->registryMock, $this->starterMock);
        $this->commandTester = new CommandTester($command);
    }

    /**
     * @test
     */
    public function okIsDisplayedWithNoTransfersAvailable(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->starterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(new CountResult(0, 0));

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                Extension::REGISTRY_NAMESPACE,
                'startCommand.lastRun',
                self::callback(
                    static fn ($subject): bool => $subject['exitCode'] === Command::SUCCESS,
                ),
            );

        $this->commandTester->execute([]);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 0 incident(s) started successfully',
            $this->commandTester->getDisplay(),
        );
    }

    /**
     * @test
     */
    public function okIsDisplayedWithTransfersAvailableAndNoErrors(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->starterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(new CountResult(3, 0));

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                Extension::REGISTRY_NAMESPACE,
                'startCommand.lastRun',
                self::callback(
                    static fn ($subject): bool => $subject['exitCode'] === Command::SUCCESS,
                ),
            );

        $this->commandTester->execute([]);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[OK] 3 incident(s) started successfully',
            $this->commandTester->getDisplay(),
        );
    }

    /**
     * @test
     */
    public function warningIsDisplayedWithErrorsOccured(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockerMock
            ->expects(self::once())
            ->method('release');

        $this->starterMock
            ->expects(self::once())
            ->method('run')
            ->willReturn(new CountResult(3, 1));

        $this->registryMock
            ->expects(self::once())
            ->method('set')
            ->with(
                Extension::REGISTRY_NAMESPACE,
                'startCommand.lastRun',
                self::callback(
                    static fn ($subject): bool => $subject['exitCode'] === Command::FAILURE,
                ),
            );

        $this->commandTester->execute([]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '[WARNING] 1 out of 3 incident(s) had errors on start',
            $this->commandTester->getDisplay(),
        );
    }

    /**
     * @test
     */
    public function warningIsDisplayedWhenLockCannotBeAcquired(): void
    {
        $this->lockerMock
            ->expects(self::once())
            ->method('acquire')
            ->willThrowException(new LockAcquireException());

        $this->lockerMock
            ->expects(self::never())
            ->method('release');

        $this->starterMock
            ->expects(self::never())
            ->method('run');

        $this->registryMock
            ->expects(self::never())
            ->method('set');

        $this->commandTester->execute([]);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            '! [NOTE] Could not acquire lock, another process is running',
            $this->commandTester->getDisplay(),
        );
    }
}
