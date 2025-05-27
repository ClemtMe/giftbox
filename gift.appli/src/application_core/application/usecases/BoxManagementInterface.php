<?php
namespace gift\appli\core\application\usecases;

interface BoxManagementInterface
{

    public function createEmptyBox(string $userId, string $name, string $description, bool $cadeau, string $messageKdo=''): array;
    public function createBoxCoffret(string $userId, string $name, string $description, bool $cadeau, int $coffretId, string $messageKdo=''): array;
    public function updateBoxPrestation(string $userId, string $boxId, string $prestationId, int $quantity): array;
    public function validateBox(string $userId, string $boxId): void;
    public function deleteBox(string $userId, string $boxId): void;

    public function getQtyPrestation(string $prestationId, string $boxId): int;

}