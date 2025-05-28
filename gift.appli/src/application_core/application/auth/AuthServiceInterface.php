<?php

namespace gift\appli\core\application\auth;

interface AuthServiceInterface
{
    public function register(string $username, string $passwordhash): string;
    public function loginByCredential(string $username, string $passwordhash): bool;
}