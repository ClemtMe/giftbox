<?php

namespace gift\appli\core\application\usecases;

use gift\appli\core\application\authorization\AuthorizationService;
use gift\appli\core\application\authorization\AuthorizationServiceInterface;
use gift\appli\core\application\exceptions\AuthorizationException;
use gift\appli\core\application\exceptions\EntityNotFoundException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\domain\entities\Box;
use gift\appli\core\domain\entities\CoffretType;
use gift\appli\core\domain\entities\User;
use Illuminate\Database\Capsule\Manager as DB;

class BoxManagement implements BoxManagementInterface
{

    private AuthorizationServiceInterface $authorizationService;

    public function __construct()
    {
        $this->authorizationService = new AuthorizationService();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionDatabase
     */
    public function createEmptyBox(string $userId, string $name, string $description, bool $cadeau, string $messageKdo = ''): string
    {
        try {
            $box = new Box();
            $box->libelle = $name;
            $box->description = $description;
            $box->kdo = $cadeau;
            $box->message_kdo = $messageKdo;
            $user = User::findOrFail($userId);
            $box->user()->associate($user);
            $box->save();
            return $box->id;
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
    public function createBoxCoffret(string $userId, string $name, string $description, bool $cadeau, int $coffretId, string $messageKdo = ''): string
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
            $box->montant = $box->prestations->sum(function ($prestation) {
                return $prestation->tarif * $prestation->pivot->quantity;
            });
            $box->save();
            return $box->id;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        } catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionDatabase("Erreur de requête : " . $e->getMessage());
        }
    }

    /**
     * @throws AuthorizationException
     * @throws ExceptionDatabase
     * @throws EntityNotFoundException
     */
    public function updateBoxPrestation(string $userId, string $boxId, string $prestationId, int $quantity): bool
    {
        if($quantity < 0) {
            return false;
        }
        if($this->authorizationService->isAuthorized($userId, AuthorizationServiceInterface::PERMISSION_UPDATE_BOX, $boxId) === false) {
            throw new AuthorizationException("Vous n'avez pas les droits pour modifier cette box");
        }
        try {
            $box = Box::findOrFail($boxId);
            DB::beginTransaction();
            $existing = $box->prestations()->where('presta_id', $prestationId)->first();

            if ($existing) {
                // Mise à jour de la quantité existante
                $box->prestations()->updateExistingPivot($prestationId, [
                    'quantite' => $quantity
                ]);
            } else {
                // Nouvelle liaison avec quantité
                $box->prestations()->attach($prestationId, ['quantite' => $quantity]);
            }
            if ($quantity <= 0) {
                // Supprimer la liaison si quantité 0
                $box->prestations()->detach($prestationId);
            }
            $box->montant = $box->prestations->sum(function ($prestation) {
                return $prestation->tarif * $prestation->pivot->quantity;
            });
            $box->save();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        } catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionDatabase("Erreur de requête : " . $e->getMessage());
        }
    }

    /**
     * @throws ExceptionDatabase
     * @throws AuthorizationException
     * @throws EntityNotFoundException
     */
    public function validateBox(string $userId, string $boxId): bool
    {

        if ($this->authorizationService->isAuthorized($userId, AuthorizationServiceInterface::PERMISSION_UPDATE_BOX, $boxId) === false) {
            throw new AuthorizationException("Vous n'avez pas les droits pour valider cette box");
        }
        try {
            $box = Box::findOrFail($boxId);
            $box->statut = 2;
            $box->save();
            return true;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        } catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionDatabase("Erreur de requête : " . $e->getMessage());
        }
    }

    /**
     * @throws ExceptionDatabase
     * @throws AuthorizationException
     * @throws EntityNotFoundException
     */
    public function deleteBox(string $userId, string $boxId): bool
    {
        try {
            if ($this->authorizationService->isAuthorized($userId, AuthorizationServiceInterface::PERMISSION_DELETE_BOX, $boxId) === false) {
                throw new AuthorizationException("Vous n'avez pas les droits pour supprimer cette box");
            }
            $box = Box::findOrFail($boxId);
            $box->prestations()->detach();
            $box->delete();
            return true;
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
    public function getQtyPrestation(string $prestationId, string $boxId): int
    {
        try {
            $box = Box::findOrFail($boxId);
            $prestation = $box->prestations()->where('presta_id', $prestationId)->first();
            $nb = $prestation ? (int) $prestation->pivot->quantite : 0;
        }catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionDatabase("Erreur de requête : " . $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        }
        return $nb;
    }

    public function getBoxByIdSessionFormat(string $boxId): array
    {
        try {
            $box = Box::find($_SESSION['box']);
            $box = [
                'id' => $box->id,
                'libelle' => $box->libelle,
                'description' => $box->description,
                'montant' => $box->montant,
                'prestations' => $box->prestations->map(function ($presta) {
                    return [
                        'libelle' => $presta->libelle,
                        'description' => $presta->description,
                        'tarif' => $presta->tarif,
                        'unite' => $presta->unite,
                        'quantite' => $presta->pivot->quantite,
                    ];
                })->toArray(),
            ];
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $box;
    }

    /**
     * @throws ExceptionDatabase
     */
    public function getBoxesByUserId(string $userId): array
    {
        try {
            $boxes = Box::where('createur_id', $userId)->get();
            return $boxes->map(function ($box) {
                return [
                    'id' => $box->id,
                    'libelle' => $box->libelle,
                    'description' => $box->description,
                    'montant' => $box->montant,
                    'statut' => $box->statut,
                ];
            })->toArray();
        } catch (\Illuminate\Database\QueryException $e) {
            throw new ExceptionDatabase("Erreur de requête : " . $e->getMessage());
        }
    }
}