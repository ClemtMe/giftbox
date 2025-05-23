<?php
namespace gift\appli\core\application\usecases;

interface BoxManagementInterface
{

    public function createEmptyBox(string $userId, string $name, string $description, bool $cadeau, string $messageKdo=''): bool;
    public function createBoxCoffret(string $userId, string $name, string $description, bool $cadeau, int $coffretId, string $messageKdo=''): bool;
    public function updateBoxPrestation(string $userId, string $boxId, string $prestationId, int $quantity): bool;
    public function validateBox(string $userId, string $boxId): bool;
    public function deleteBox(string $userId, string $boxId): bool;

}