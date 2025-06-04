<?php

namespace gift\appli\core\application\authorization;

use gift\appli\core\application\auth\AuthService;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\domain\entities\Box;
use gift\appli\core\domain\exceptions\EntityNotFoundException;

class AuthorizationService implements AuthorizationServiceInterface
{
    /**
     * @throws ExceptionDatabase
     * @throws EntityNotFoundException
     */
    public function isAuthorized(string $userId, string $action, ?string $resourceId = null): bool
    {
        $user = (new AuthService())->getUserById($userId);
        if ($user['role'] >= 100) {
            return true;
        }

        switch ($action) {
            case AuthorizationServiceInterface::PERMISSION_READ_BOX:
            case AuthorizationServiceInterface::PERMISSION_CREATE_BOX:
                return $user['role'] >= 1;
            case AuthorizationServiceInterface::PERMISSION_UPDATE_BOX:
            case AuthorizationServiceInterface::PERMISSION_DELETE_BOX:
                if ($resourceId === null) {
                    return false;
                }
                return $this->isBoxOwner($userId, $resourceId);
            default:
                return false;
        }
    }

    /**
     * @throws ExceptionDatabase
     */
    public function isAdmin(string $userId): bool
    {
        $user = (new AuthService())->getUserById($userId);
        if($user['role'] >= 100){
            return true;
        }
        return false;
    }

    /**
     * @throws ExceptionDatabase
     * @throws EntityNotFoundException
     */
    public function isBoxOwner(string $userId, string $boxId): bool
    {
        $user = (new AuthService())->getUserById($userId);
        if ($user['role'] >= 100) {
            return true;
        }

        try{
            $box = Box::findOrFail($boxId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Box not found");
        }

        if ($box->createur_id === $userId) {
            return true;
        }

        return false;
    }
}