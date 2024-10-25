<?php

use Meisterwerk\Core\ExceptionHandler;
use PHPUnit\Framework\TestCase;

class ExceptionHandlerTest extends TestCase
{
    public function testMailingCallbackWithThrowOn(): void
    {
        $originException = new \Exception('MailingCallBackTestException');
        $handler = fn () => (print 'mailing callback success');
        $scope = 'mailingCallbackTest';

        $hasThrownUnexpectedException = false;
        try {
            ExceptionHandler::handleUnexpectedException($originException, $scope, $handler);
        } catch (RuntimeException $e) {
            $hasThrownUnexpectedException = true;
            $this->assertSame($e->getMessage(), 'Unexpected error (' . $scope . ')');
            $this->assertSame($originException, $e->getPrevious());
        }
        $this->assertTrue($hasThrownUnexpectedException);
    }

    public function testUnexpectedErrorException(): void
    {
        $originException = new \Exception('MailingCallBackTestException');
        $mailingCallbackException = new \Exception('exception in mailing callback');
        $scope = 'unexpectedErrorTest';
        $handler = fn () => (throw $mailingCallbackException);

        $hasThrownUnexpectedException = false;
        try {
            ExceptionHandler::handleUnexpectedException($originException, $scope, $handler);
        } catch (RuntimeException $e) {
            $hasThrownUnexpectedException = true;
            $this->assertSame('Unexpected error and failed error handling (' . $scope . ')', $e->getMessage());
            $this->assertStringStartsWith(
                $mailingCallbackException->getMessage() . ' Trace: ',
                $e->getPrevious()->getMessage()
            );
            $this->assertSame($originException, $e->getPrevious()->getPrevious());
        }
        $this->assertTrue($hasThrownUnexpectedException);
    }
}
