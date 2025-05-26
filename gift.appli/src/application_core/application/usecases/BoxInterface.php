<?php
namespace gift\appli\core\application\usecases;

interface BoxInterface
{
    public function getBoxByToken(string $token): ?Box;
}
