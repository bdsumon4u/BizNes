<?php

namespace App\Exceptions;

use Exception;

class UndefinedEnumCaseError extends Exception
{
    /**
     * @param  class-string  $enum
     * @return void
     */
    public function __construct(string $enum, string $case)
    {
        parent::__construct(
            message: "Undefined constant {$enum}::{$case}.",
        );
    }
}
