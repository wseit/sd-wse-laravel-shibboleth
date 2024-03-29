<?php

namespace Jhu\Wse\LaravelShibboleth\Exceptions;

use Exception;
use Jhu\Wse\LaravelShibboleth\Exceptions\Concerns\HasContext;

class MissingUserAuthenticationFieldException extends Exception
{
    use HasContext;
    
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->setContext($context);
    }

}
