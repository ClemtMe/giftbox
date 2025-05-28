<?php

namespace gift\appli\webui\providers;

use gift\appli\core\application\auth\AuthService;
use gift\appli\core\application\auth\AuthServiceInterface;
use gift\appli\core\application\exceptions\AuthentificationException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use Slim\Exception\HttpInternalServerErrorException;

class AuthProvider implements AuthProviderInterface
{
    private AuthServiceInterface $authService;

    public function __construct(){
        $this->authService = new AuthService();
    }

    public function register(string $username, string $password): void
    {
        try {
            $id = $this->authService->register($username, $password);
            $_SESSION['user'] = $id;
        } catch (ExceptionDatabase $e) {
            throw new HttpInternalServerErrorException("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    public function loginByCredential(string $username, string $password): void
    {
        try {
            $id = $this->authService->loginByCredential($username, $password);
            $_SESSION['user'] = $id;
        } catch (AuthentificationException $e) {
            throw new Slim\Exception\HttpUnauthorizedException("Authentification échouée : " . $e->getMessage());
        } catch (ExceptionDatabase $e) {
            throw new HttpInternalServerErrorException("Erreur de base de donnée : " . $e->getMessage());
        }
    }

    public function getSignedInUser(): array
    {
        if (!isset($_SESSION['user'])) {
            return [];
        }

        try {
            return $this->authService->getUserById($_SESSION['user']);
        } catch (ExceptionDatabase $e) {
            throw new HttpInternalServerErrorException("Erreur de base de donnée : " . $e->getMessage());
        }
    }
}