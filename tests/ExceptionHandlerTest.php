<?php

use Meisterwerk\Core\Exception\UnexpectedErrorException;
use Meisterwerk\Core\ExceptionHandler;
use PHPUnit\Framework\TestCase;

class ExceptionHandlerTest extends TestCase
{
    public function testMailingCallbackWithoutThrowOn(): void
    {
        $exception = new \Exception('MailingCallBackTestException');
        $handler = fn () => (print 'mailing callback success');

        $this->expectOutputString('mailing callback success');

        ExceptionHandler::handleWithCare($exception, 'mailingCallbackTest', $handler, false);
    }

    public function testMailingCallbackWithThrowOn(): void
    {
        $exception = new \Exception('MailingCallBackTestException');
        $handler = fn () => (print 'mailing callback success');
        $scope = 'mailingCallbackTest';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unexpected error (' . $scope . ')');
        $this->expectExceptionCode(0);

        ExceptionHandler::handleWithCare($exception, $scope, $handler);
    }

    public function testUnexpectedErrorException(): void
    {
        $exception = new UnexpectedErrorException('UnexpectedErrorTestException');
        $scope = 'unexpectedErrorTest';
        $handler = fn () => (throw new \Exception('exception in mailing callback'));

        $this->expectException(UnexpectedErrorException::class);
        $this->expectExceptionMessage('Unexpected error and failed error handling (' . $scope . ')');
        $this->expectExceptionCode(0);

        ExceptionHandler::handleWithCare($exception, $scope, $handler);
    }

}