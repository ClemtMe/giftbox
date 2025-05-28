<?php

namespace gift\appli\core\application\auth;

interface AuthServiceInterface
{
    public function register(string $username, string $password): string;
    public function loginByCredential(string $username, string $password): string;
    public function getUserById(string $userId): array;
}