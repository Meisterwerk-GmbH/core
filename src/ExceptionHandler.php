<?php

namespace Meisterwerk\Core;

class ExceptionHandler
{
    public static function handleUnexpectedException(
        \Exception|\Error $e,
        string $scope,
        $mailingCallback
    ): void
    {
        self::handleWithCare($e, $scope, $mailingCallback);
    }

    /**
     * @deprecated please use `handleUnexpectedException`
     * Cases where the origin-exception must not be thrown-on should be handled directly because no special exception
     * nesting is needed.
     * It seems that unexpected-exceptions should be thrown-on in any case. Otherwise, this deprecation should be
     * reconsidered.
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

    /**
     * @deprecated please use `handleUnexpectedException`
     * Expected exceptions should be handled directly, because no special exception nesting will be needed.
     */
    public static function handleWithCare(
        \Exception|\Error $e,
        string $scope,
        $mailingCallback,
        bool $unexpected = true
    ): void {
        self::handleWithCareInner($e, $scope, $mailingCallback, $unexpected);
    }

    /**
     * This function is wrapped because we want to suppress the exception-handling warning for the caller of
     * the handleWithCare function.
     * After removing the deprecated callers, this function can be simplified.
     */
    private static function handleWithCareInner(
        \Exception|\Error $originException,
        string $scope,
        $mailingCallback,
        bool $unexpected = true
    ): void {
        try {
            $mailingCallback($scope);
        } catch (\Exception|\Error $mailingException) {
            if ($unexpected) {
                $mailingMessageAndStack
                    = $mailingException->getMessage() . ' Trace: ' . $mailingException->getTraceAsString();
                $wrappedMailingException
                    = new \Exception($mailingMessageAndStack, $mailingException->getCode(), $originException);
                throw new \RuntimeException(
                    'Unexpected error and failed error handling (' . $scope . ')',
                    0,
                    $wrappedMailingException
                );
            } else {
                throw $mailingException;
            }
        }
        if ($unexpected) {
            throw new \RuntimeException('Unexpected error (' . $scope . ')', 0, $originException);
        }
    }
}
