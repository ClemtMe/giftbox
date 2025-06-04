<?php

namespace gift\appli\webui\exceptions;

class BoxAccesException extends \Exception
{
    public function __construct($message = "Box access erreur")
    {
        parent::__construct($message);
    }
}