<?php
namespace gift\appli\core\application\usecases;

use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\domain\entities\Categorie;
use gift\appli\core\domain\entities\CoffretType;
use gift\appli\core\domain\entities\Prestation;
use gift\appli\core\domain\entities\Theme;

class Catalogue implements CatalogueInterface
{

    /**
     * @throws ExceptionDatabase
     */
    public function getCategories(): array{
        try {
            $categories = Categorie::all();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ExceptionDatabase("Table Categorie introuvable");
        }
        return $categories->toArray();
    }

    /**
     * @throws ExceptionDatabase
     */
    public function getCategorieById(int $id): array
    {
        try {
            $categorie = Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ExceptionDatabase("Categorie introuvable");
        }
        return $categorie->toArray();
    }

    /**
     * @throws ExceptionDatabase
     */
    public function getPrestationById(int $id): array
    {
        try {
            $prestation = Prestation::find($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ExceptionDatabase("Prestation introuvable");
        }
        return $prestation->toArray();
    }

    /**
     * @throws ExceptionDatabase
     */
    public function getPrestationsByCategorie(int $categ_id): array
    {
        try {
            $categorie = Categorie::findOrFail($categ_id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ExceptionDatabase("Categorie introuvable");
        }
        return $categorie->prestations->toArray();
    }

    /**
     * @throws ExceptionDatabase
     */
    public function getPrestationsByCoffret(int $coffret_id): array
    {
        try {
            $coffret = CoffretType::findOrFail($coffret_id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ExceptionDatabase("Coffret introuvable");
        }
        return $coffret->prestations->toArray();
    }

    /**
     * @throws ExceptionDatabase
     */
    public function getThemesCoffrets(): array
    {
        try {
            $themes = Theme::all();
        } catch ( \Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ExceptionDatabase("Table Theme introuvable");
        }
        return $themes->toArray();
    }

    /**
     * @throws ExceptionDatabase
     */
    public function getCoffretById(int $id): array
    {
        try {
            $coffret = Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ExceptionDatabase("Coffret introuvable");
        }
        return $coffret->toArray();
    }

    /**
     * @throws ExceptionDatabase
     */
    public function getCoffrets(): array
    {
        try {
            $coffrets = CoffretType::all();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ExceptionDatabase("Table Coffret introuvable");
        }
        return $coffrets->toArray();
    }
}