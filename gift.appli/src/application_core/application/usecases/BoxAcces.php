<?php

namespace gift\appli\core\application\usecases;

use gift\appli\core\application\usecases\BoxInterface;
use gift\appli\core\domain\entities\Box;

class BoxAcces implements BoxInterface
{
    public function getBoxByToken(string $token): ?Box
    {
        return Box::where('token', $token)->first();
    }
}
