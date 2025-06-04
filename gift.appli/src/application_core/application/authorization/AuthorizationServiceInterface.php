<?php

namespace gift\appli\core\application\authorization;

interface AuthorizationServiceInterface
{
    const PERMISSION_UPDATE_BOX = 'update_box';
    const PERMISSION_CREATE_BOX = 'create_box';
    const PERMISSION_READ_BOX = 'read_box';


    public function isAuthorized(string $userId, string $action, string $resourceId=null): bool;
    public function isBoxOwner(string $userId, string $boxId): bool;
    public function isAdmin(string $userId): bool;
}