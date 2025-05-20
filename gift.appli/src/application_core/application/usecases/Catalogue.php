<?php
namespace gift\appli\core\application\usecases;

use gift\appli\core\domain\entities\Categorie;
use gift\appli\core\domain\entities\Prestation;
use gift\appli\core\domain\entities\Theme;

class Catalogue implements CatalogueInterface
{

    public function getCategories(): array{
        $categories = Categorie::all();
        return $categories->toArray();
    }

    public function getCategorieById(int $id): array
    {
        try {
            $categorie = Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception("Categorie introuvable");
        }
        return $categorie->toArray();
    }

    public function getPrestationById(int $id): array
    {
        try {
            $prestation = Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception("Prestation introuvable");
        }
        return $prestation->toArray();
    }

    public function getPrestationsByCategorie(int $categ_id): array
    {
        try {
            $categorie = Categorie::findOrFail($categ_id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception("Categorie introuvable");
        }
        return $categorie->prestations->toArray();
    }

    public function getThemesCoffrets(): array
    {
        $themes = Theme::all();
        return $themes->toArray();
    }

    public function getCoffretById(int $id): array
    {
        try {
            $coffret = Categorie::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception("Coffret introuvable");
        }
        return $coffret->toArray();
    }
}