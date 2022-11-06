<?php

namespace App\Tests\EventListener;

use App\EventListener\RequestTransactionListener;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

use function PHPUnit\Framework\exactly;

use PHPUnit\Framework\MockObject\MockObject;

use function PHPUnit\Framework\never;
use function PHPUnit\Framework\once;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @internal
 */
class RequestTransactionListenerTest extends TestCase {
    private MockObject|Connection $connection;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|HttpKernelInterface $kernel;

    private Request|MockObject $request;
    private RequestTransactionListener $requestTransactionListener;

    protected function setUp(): void {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->connection = $this->createMock(Connection::class);
        $this->entityManager->method('getConnection')->willReturn($this->connection);

        $this->kernel = $this->createMock(HttpKernelInterface::class);
        $this->request = $this->createMock(Request::class);

        $this->requestTransactionListener = new RequestTransactionListener($this->entityManager);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testIgnoresRequestEventForSubRequest() {
        $this->entityManager->expects(never())->method('getConnection');

        $this->requestTransactionListener->startTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::SUB_REQUEST
            )
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testStartsTransactionForRequestEventForMainRequest() {
        $this->entityManager->expects(exactly(2))->method('getConnection');

        $this->requestTransactionListener->startTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testThrowsIfMultipleTransactionsStarted() {
        $this->entityManager->expects(exactly(2))->method('getConnection');
        $this->requestTransactionListener->startTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );

        $this->expectException(\RuntimeException::class);
        $this->requestTransactionListener->startTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testIgnoresResponseEventForSubRequest() {
        $this->entityManager->expects(never())->method('getConnection');
        $this->entityManager->expects(never())->method('flush');

        $this->requestTransactionListener->commitTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::SUB_REQUEST
            )
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testCommitsForResponseEventForMainRequest() {
        $this->entityManager->expects(exactly(4))->method('getConnection');
        $this->connection->method('getTransactionNestingLevel')->willReturn(1);

        $this->requestTransactionListener->startTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );

        $this->requestTransactionListener->commitTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testThrowsOnCommitIfNoTransactionStarted() {
        $this->entityManager->expects(never())->method('getConnection');

        $this->expectException(\RuntimeException::class);
        $this->requestTransactionListener->commitTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testThrowsOnCommitForUnexpectedTransactionNestingLevel() {
        $this->entityManager->expects(exactly(3))->method('getConnection');
        $this->connection
            ->method('getTransactionNestingLevel')
            ->willReturnOnConsecutiveCalls(1, 2)
        ;

        $this->requestTransactionListener->startTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );

        $this->expectException(\RuntimeException::class);
        $this->requestTransactionListener->commitTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );
    }

    /**
     * @dataProvider methodsWhichDontChangeState
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function testIgnoresExceptionsForRequestsWhichDontChangeState(string $method) {
        $this->request->expects(once())->method('getMethod')->willReturn($method);
        $this->entityManager->expects(never())->method('getConnection');
        $this->entityManager->expects(never())->method('clear');

        $this->requestTransactionListener->rollbackTransaction(
            new ExceptionEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST,
                new \RuntimeException()
            )
        );
    }

    /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
    public static function methodsWhichDontChangeState(): array {
        return ['GET' => ['GET'], 'OPTIONS' => ['OPTIONS']];
    }

    /**
     * @dataProvider methodsWhichChangeState
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function testRollsBackTransactionForExceptionsForOtherMethods(string $method) {
        $this->request->expects(once())->method('getMethod')->willReturn($method);
        $this->entityManager->expects(exactly(4))->method('getConnection');
        $this->entityManager->expects(once())->method('clear');
        $this->requestTransactionListener->startTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );

        $this->requestTransactionListener->rollbackTransaction(
            new ExceptionEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST,
                new \RuntimeException()
            )
        );
    }

    /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
    public static function methodsWhichChangeState(): array {
        return ['PUT' => ['PUT'], 'POST' => ['POST'], 'DELETE' => ['DELETE']];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testThrowsOnRollbackIfNoTransactionStartedButStillTriesToRollBack() {
        $this->entityManager->expects(once())->method('getConnection');

        $this->expectException(\RuntimeException::class);
        $this->requestTransactionListener->rollbackTransaction(
            new ExceptionEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST,
                new \RuntimeException()
            )
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testThrowsOnRollbackForUnexpectedTransactionNestingLevelButStillTriesToRollBack() {
        $this->entityManager->expects(exactly(4))->method('getConnection');
        $this->connection
            ->method('getTransactionNestingLevel')
            ->willReturnOnConsecutiveCalls(1, 2)
        ;

        $this->requestTransactionListener->startTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );

        $this->expectException(\RuntimeException::class);
        $this->requestTransactionListener->rollbackTransaction(
            new ExceptionEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST,
                new \RuntimeException()
            )
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function testStillClearsEntityManagerWhenRollbackThrowsException() {
        $this->request->expects(once())->method('getMethod')->willReturn('DELETE');
        $this->connection
            ->expects(once())
            ->method('rollback')
            ->willThrowException(new \RuntimeException())
        ;
        $this->entityManager->expects(exactly(4))->method('getConnection');
        $this->entityManager->expects(once())->method('clear');

        $this->requestTransactionListener->startTransaction(
            new KernelEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );

        $this->expectException(\RuntimeException::class);
        $this->requestTransactionListener->rollbackTransaction(
            new ExceptionEvent(
                $this->kernel,
                $this->request,
                HttpKernelInterface::MAIN_REQUEST,
                new \RuntimeException()
            )
        );
    }
}
