<?php
namespace gift\appli\core\domain\exceptions;

class InvalidTokenException extends \Exception
{
    public function __construct($message = "Invalid token")
    {
        parent::__construct($message);
    }
}