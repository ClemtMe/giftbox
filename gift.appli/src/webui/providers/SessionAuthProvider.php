<?php

namespace gift\appli\webui\providers;

use gift\appli\core\application\auth\AuthService;
use gift\appli\core\application\auth\AuthServiceInterface;
use gift\appli\core\application\exceptions\AuthentificationException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use Slim\Exception\HttpInternalServerErrorException;

class SessionAuthProvider implements AuthProviderInterface
{
    private AuthServiceInterface $authService;
    private String $sessionKey = 'auth_user';

    public function __construct(){
        $this->authService = new AuthService();
    }

    /**
     * @throws ExceptionDatabase
     * @throws AuthentificationException
     */
    public function register(string $username, string $password): void
    {
        $id = $this->authService->register($username, $password);
        $_SESSION[$this->sessionKey] = $this->authService->getUserById($id);
    }

    /**
     * @throws ExceptionDatabase
     * @throws AuthentificationException
     */
    public function loginByCredential(string $username, string $password): void
    {
        $id = $this->authService->loginByCredential($username, $password);
        $_SESSION[$this->sessionKey] = $this->authService->getUserById($id);
    }

    /**
     * @throws ExceptionDatabase
     */
    public function getSignedInUser(): array
    {
        if (!isset($_SESSION[$this->sessionKey])) {
            return [];
        }

        return $_SESSION[$this->sessionKey];
    }
}