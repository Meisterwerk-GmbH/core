<?php

namespace Meisterwerk\Core;

use Exception;

class ExceptionHandler
{
    public static function handleUnexpectedError(
        \Exception|\Error $e,
        string $scope,
        $mailingCallback,
        bool $throwOn = true
    ): void
    {
        try {
            $mailingCallback($scope);
        } catch (\Exception|\Error) {
            throw new \Exception('Unexpected error and failed error handling (' . $scope . ')', 0, $e);
        }
        if ($throwOn) {
            throw new \Exception('Unexpected error (' . $scope . ')', 0, $e);
        }
    }

}
