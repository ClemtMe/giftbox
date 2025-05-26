<?php

namespace gift\appli\core\application\usecases;

use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\domain\entities\Box;
use gift\appli\core\domain\entities\CoffretType;
use gift\appli\core\domain\entities\User;
use gift\appli\core\domain\exceptions\EntityNotFoundException;

class BoxManagement implements BoxManagementInterface
{

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionDatabase
     */
    public function createEmptyBox(string $userId, string $name, string $description, bool $cadeau, string $messageKdo = ''): array
    {
        try {
            $box = new Box();
            $box->name = $name;
            $box->description = $description;
            $box->cadeau = $cadeau;
            $box->messageKdo = $messageKdo;
            $user = User::findOrFail($userId);
            $box->user()->associate($user);
            $box->save();
            return $box->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        } catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionDatabase("Erreur de requête : " . $e->getMessage());
        }
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionDatabase
     */
    public function createBoxCoffret(string $userId, string $name, string $description, bool $cadeau, int $coffretId, string $messageKdo = ''): array
    {
        try {
            $box = new Box();
            $box->name = $name;
            $box->description = $description;
            $box->cadeau = $cadeau;
            $box->messageKdo = $messageKdo;
            $user = User::findOrFail($userId);
            $box->user()->associate($user);
            $coffret = CoffretType::findOrFail($coffretId);
            $prestationIds = $coffret->prestations()->pluck('prestations.id')->toArray();
            $box->prestations()->attach($prestationIds);
            return $box->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        } catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionDatabase("Erreur de requête : " . $e->getMessage());
        }
    }

    public function updateBoxPrestation(string $userId, string $boxId, string $prestationId, int $quantity): array
    {
        // TODO: Implement updateBoxPrestation() method.
    }

    public function validateBox(string $userId, string $boxId): void
    {
        // TODO: Implement validateBox() method.
    }

    public function deleteBox(string $userId, string $boxId): void
    {
        // TODO: Implement deleteBox() method.
    }
}