<?php

namespace gift\appli\webui\providers;

interface BoxServiceProviderInterface
{
    public function getBoxByToken(string $token): array;
    public function generateBoxAccesLink(string $boxid): string;
}