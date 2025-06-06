<?php

namespace gift\appli\core\application\usecases;

use gift\appli\core\application\authorization\AuthorizationService;
use gift\appli\core\application\authorization\AuthorizationServiceInterface;
use gift\appli\core\application\exceptions\AuthorizationException;
use gift\appli\core\application\exceptions\EntityNotFoundException;
use gift\appli\core\application\exceptions\ExceptionInterne;
use gift\appli\core\domain\entities\Box;
use gift\appli\core\domain\entities\CoffretType;
use gift\appli\core\domain\entities\User;
use Illuminate\Database\Capsule\Manager as DB;
use Ramsey\Uuid\Uuid;

class BoxManagement implements BoxManagementInterface
{

    private AuthorizationServiceInterface $authorizationService;

    public function __construct()
    {
        $this->authorizationService = new AuthorizationService();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function createEmptyBox(string $userId, string $name, string $description, bool $cadeau, string $messageKdo = ''): string
    {
        try {
            $box = new Box();
            $box->id = Uuid::uuid4()->toString();
            $box->token = '';
            $box->libelle = $name;
            $box->description = $description;
            $box->kdo = $cadeau;
            $box->message_kdo = $messageKdo;
            $box->statut = 1;
            $user = User::findOrFail($userId);
            $box->user()->associate($user);
            $box->save();
            return $box->id;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        } catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function createBoxCoffret(string $userId, string $name, string $description, bool $cadeau, int $coffretId, string $messageKdo = ''): string
    {
        try {
            DB::beginTransaction();
            $box = new Box();
            $box->id = Uuid::uuid4()->toString();
            $box->token = '';
            $box->libelle = $name;
            $box->description = $description;
            $box->kdo = $cadeau;
            $box->message_kdo = $messageKdo;
            $box->statut = 1;
            $box->save();
            $user = User::findOrFail($userId);
            $box->user()->associate($user);
            $coffret = CoffretType::findOrFail($coffretId);
            $prestationIds = $coffret->prestations()->pluck('id')->toArray();
            $data = [];
            foreach ($prestationIds as $id) {
                $data[$id] = ['quantite' => 1];
            }
            $box->prestations()->attach($data);
            $box->montant = $box->prestations->sum(function ($prestation) {
                return $prestation->tarif * $prestation->pivot->quantite;
            });
            $box->save();
            DB::commit();
            return $box->id;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        } catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
    }

    /**
     * @throws AuthorizationException
     * @throws ExceptionInterne
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
                return $prestation->tarif * $prestation->pivot->quantite;
            });
            $box->save();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        } catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
    }

    /**
     * @throws ExceptionInterne
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
            if ($box->statut !== 1) {
                throw new AuthorizationException("La box n'est pas en cours de création");
            }
            // Vérification que la box a au moins deux prestation
            if ($box->prestations->count() < 2) {
                throw new AuthorizationException("La box doit contenir au moins deux prestations différentes pour être validée");
            }
            $box->statut = 2;
            $box->save();
            return true;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        } catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
    }

    /**
     * @throws ExceptionInterne
     * @throws AuthorizationException
     * @throws EntityNotFoundException
     */
    public function deleteBox(string $userId, string $boxId): bool
    {
        if ($this->authorizationService->isAuthorized($userId, AuthorizationServiceInterface::PERMISSION_DELETE_BOX, $boxId) === false) {
            throw new AuthorizationException("Vous n'avez pas les droits pour supprimer cette box");
        }
        try {
            $box = Box::findOrFail($boxId);
            $box->prestations()->detach();
            $box->delete();
            return true;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table introuvable");
        } catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function getQtyPrestation(string $prestationId, string $boxId): int
    {
        try {
            $box = Box::findOrFail($boxId);
            $prestation = $box->prestations()->where('presta_id', $prestationId)->first();
            $nb = $prestation ? (int) $prestation->pivot->quantite : 0;
        }catch (\Illuminate\Database\QueryException $e){
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
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
     * @throws ExceptionInterne
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
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
    }
}