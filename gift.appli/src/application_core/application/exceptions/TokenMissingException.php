<?php
namespace gift\appli\core\application\exceptions;
class TokenMissingException extends \Exception
{
    public function __construct($message = "Token is missing")
    {
        parent::__construct($message);
    }
}