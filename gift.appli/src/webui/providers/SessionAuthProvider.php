<?php

namespace gift\appli\webui\providers;

use gift\appli\core\application\auth\AuthService;
use gift\appli\core\application\auth\AuthServiceInterface;
use gift\appli\core\application\exceptions\AuthentificationException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\webui\exceptions\ProviderAuthentificationException;
use Slim\Exception\HttpInternalServerErrorException;

class SessionAuthProvider implements AuthProviderInterface
{
    private AuthServiceInterface $authService;
    private String $sessionKey = 'auth_user';

    public function __construct(){
        $this->authService = new AuthService();
    }

    /**
     * @throws ProviderAuthentificationException
     */
    public function register(string $username, string $password): void
    {
        try {
            $id = $this->authService->register($username, $password);
        } catch (AuthentificationException | ExceptionDatabase $e) {
            throw new ProviderAuthentificationException("Erreur d'authentification : " . $e->getMessage());
        }
        try {
            $_SESSION[$this->sessionKey] = $this->authService->getUserById($id);
        } catch (ExceptionDatabase $e) {
            throw new ProviderAuthentificationException("Erreur d'authentification : " . $e->getMessage());
        }
    }

    /**
     * @throws ProviderAuthentificationException
     */
    public function loginByCredential(string $username, string $password): void
    {
        try {
            $id = $this->authService->loginByCredential($username, $password);
        } catch (AuthentificationException | ExceptionDatabase $e) {
            throw new ProviderAuthentificationException("Erreur d'authentification : " . $e->getMessage());
        }
        try {
            $_SESSION[$this->sessionKey] = $this->authService->getUserById($id);
        } catch (ExceptionDatabase $e) {
            throw new ProviderAuthentificationException("Erreur d'authentification : " . $e->getMessage());
        }
    }

    public function getSignedInUser(): array
    {
        if (!isset($_SESSION[$this->sessionKey])) {
            return [];
        }

        return $_SESSION[$this->sessionKey];
    }

    /**
     * @throws ProviderAuthentificationException
     */
    public function logout(): void
    {
        if (isset($_SESSION[$this->sessionKey])) {
            unset($_SESSION[$this->sessionKey]);
        } else {
            throw new ProviderAuthentificationException("Aucun utilisateur connecté pour se déconnecter.");
        }
    }
}