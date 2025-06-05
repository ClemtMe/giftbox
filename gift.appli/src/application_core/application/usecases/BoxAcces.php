<?php

namespace gift\appli\core\application\usecases;

use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\exceptions\InvalidTokenException;
use gift\appli\core\application\exceptions\TokenMissingException;
use gift\appli\core\domain\entities\Box;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoxAcces implements BoxInterface
{
    /**
     * @throws TokenMissingException
     * @throws InvalidTokenException
     * @throws EntityNotFoundException
     */
    public function accesBoxByToken(string $token): array
    {
        if (empty($token)) {
            throw new TokenMissingException();
        }

        DB::beginTransaction();

        try {
            $box = Box::where('token', $token)->first();
        } catch (\Exception $e) {
            throw new EntityNotFoundException($e->getMessage());
        }

        if ($box === null) {
            throw new InvalidTokenException("Aucune box trouvée pour le token $token.");
        }

        if ($box->statut !== 3) {
            throw new InvalidTokenException("La box associée au token $token n'est pas livrée.");
        }

        $box->statut = 4;
        $box->save();
        DB::commit();

        return [
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

    }

    /**
     * @throws TokenMissingException
     * @throws ExceptionDatabase
     * @throws InvalidTokenException
     * @throws EntityNotFoundException
     */
    public function setBoxToken(string $boxid, string $token): void
    {
        if (empty($token)) {
            throw new TokenMissingException();
        }

        $box = Box::where('token', $token)->first();

        if ($box !== null) {
            throw new InvalidTokenException("Une box avec le token $token existe déjà.");
        }

        try {
            $box = Box::findorfail($boxid);
        } catch (ModelNotFoundException $e) {
            throw new EntityNotFoundException("Box $boxid introuvable");
        } catch (QueryException $e) {
            throw new ExceptionDatabase("Erreur de requête : " . $e->getMessage());
        }

        if ($box->statut !== 2) {
            throw new InvalidTokenException("Impossible d'associer un token à la box $boxid car son statut n'est pas 'Validée'.");
        }

        DB::beginTransaction();
        $box->token = $token;
        $box->statut = 3;
        DB::commit();
        $box->save();
    }
}
