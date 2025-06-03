<?php

namespace gift\appli\webui\providers;

interface AuthProviderInterface
{
    public function register(string $username, string $password): void;
    public function loginByCredential(string $username, string $password): void;
    public function getSignedInUser(): array;
}