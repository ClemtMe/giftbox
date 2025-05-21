<?php

namespace gift\appli\core\application\exceptions;
class ExceptionDatabase extends \Exception
{
    public function __construct($message = "Erreur de base de données", $code = 500, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}