<?php
namespace gift\appli\core\application\usecases;

interface BoxManagementInterface
{

    public function createEmptyBox(string $userId, string $name, string $description, bool $cadeau, string $messageKdo=''): string;
    public function createBoxCoffret(string $userId, string $name, string $description, bool $cadeau, int $coffretId, string $messageKdo=''): string;
    public function updateBoxPrestation(string $userId, string $boxId, string $prestationId, int $quantity): bool;
    public function validateBox(string $userId, string $boxId): bool;
    public function deleteBox(string $userId, string $boxId): bool;
    public function getQtyPrestation(string $prestationId, string $boxId): int;
    public function getBoxesByUserId(string $userId): array;
    public function getBoxByIdSessionFormat(string $boxId): array;

}