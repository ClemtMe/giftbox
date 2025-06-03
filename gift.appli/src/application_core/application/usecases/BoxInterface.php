<?php
namespace gift\appli\core\application\usecases;

interface BoxInterface
{
    public function accesBoxByToken(string $token): array;
    public function setBoxToken(string $boxid, string $token): void;
}
