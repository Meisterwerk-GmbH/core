<?php

namespace Meisterwerk\Core;

use Meisterwerk\Core\Exception\UnexpectedErrorException;

class ExceptionHandler
{
    /**
     * @deprecated please use the handleWithCare-function
     */
    public static function handleUnexpectedError(
        \Exception|\Error $e,
        string $scope,
        $mailingCallback,
        bool $throwOn = true
    ): void
    {
        self::handleWithCare($e, $scope, $mailingCallback, $throwOn);
    }

    public static function handleWithCare(
        \Exception|\Error $e,
        string $scope,
        $mailingCallback,
        bool $throwOn = true
    ): void {
        self::handleWithCareInner($e, $scope, $mailingCallback, $throwOn);
    }

    /**
     * This function is wrapped because we want to suppress the exception-handling warning for the caller of
     * the handleWithCare function.
     */
    private static function handleWithCareInner(
        \Exception|\Error $originException,
        string $scope,
        $mailingCallback,
        bool $throwOn = true
    ): void {
        try {
            $mailingCallback($scope);
        } catch (\Exception|\Error $mailingException) {
            $mailingMessageAndStack = $mailingException->getMessage() . ' Trace: ' . $mailingException->getTraceAsString();
            $wrappedMailingException = new \Exception($mailingMessageAndStack, $mailingException->getCode(), $originException);
            throw new UnexpectedErrorException('Unexpected error and failed error handling (' . $scope . ')', 0, $wrappedMailingException);
        }
        if ($throwOn) {
            throw new UnexpectedErrorException('Unexpected error (' . $scope . ')', 0, $originException);
        }
    }
}
