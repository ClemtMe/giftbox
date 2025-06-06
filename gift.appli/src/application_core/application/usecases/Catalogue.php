<?php
namespace gift\appli\core\application\usecases;

use Couchbase\QueryException;
use gift\appli\core\application\exceptions\EntityNotFoundException;
use gift\appli\core\application\exceptions\ExceptionInterne;
use gift\appli\core\domain\entities\Categorie;
use gift\appli\core\domain\entities\CoffretType;
use gift\appli\core\domain\entities\Prestation;
use gift\appli\core\domain\entities\Theme;

class Catalogue implements CatalogueInterface
{

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function getCategories(): array{
        try {
            $categories = Categorie::all();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table Categorie introuvable");
        } catch (QueryException $e) {
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
        return $categories->toArray();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function getCategorieById(int $id): array
    {
        try {
            $categorie = Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Categorie $id introuvable");
        } catch (QueryException $e) {
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
        return $categorie->toArray();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function getPrestationById(string $id): array
    {
        try {
            $prestation = Prestation::find($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Prestation $id introuvable");
        } catch (QueryException $e) {
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
        return $prestation->toArray();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function getPrestationsByCategorie(int $categ_id): array
    {
        try {
            $categorie = Categorie::findOrFail($categ_id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Categorie $categ_id introuvable");
        } catch (QueryException $e) {
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
        return $categorie->prestations->toArray();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function getPrestationsByCoffret(int $coffret_id): array
    {
        try {
            $coffret = CoffretType::findOrFail($coffret_id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Coffret $coffret_id introuvable");
        } catch (QueryException $e) {
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
        return $coffret->prestations->toArray();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function getThemesCoffrets(): array
    {
        try {
            $themes = Theme::all();
        } catch ( \Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table Theme introuvable");
        } catch (QueryException $e) {
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
        return $themes->toArray();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function getCoffretById(int $id): array
    {
        try {
            $coffret = Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Categorie $id introuvable");
        } catch (QueryException $e) {
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
        return $coffret->toArray();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function getCoffrets(): array
    {
        try {
            $coffrets = CoffretType::all();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table Coffret introuvable");
        } catch (QueryException $e) {
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
        return $coffrets->toArray();
    }

    /**
     * @throws EntityNotFoundException
     * @throws ExceptionInterne
     */
    public function getPrestations(): array
    {
        try {
            $prestations = Prestation::all();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EntityNotFoundException("Table Prestation introuvable");
        } catch (QueryException $e) {
            throw new ExceptionInterne("Erreur de requête : " . $e->getMessage());
        }
        return $prestations->toArray();
    }
}