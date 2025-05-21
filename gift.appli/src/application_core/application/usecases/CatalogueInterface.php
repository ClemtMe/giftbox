<?php
namespace gift\appli\core\application\usecases;
use gift\appli\core\domain\entities\Categorie;
use gift\appli\core\domain\entities\CoffretType;
use gift\appli\core\domain\entities\Prestation;

Interface CatalogueInterface{
    public function getCategories(): array;
    public function getCategorieById(int $id): Categorie;
    public function getPrestationById(int $id): Prestation;
    public function getPrestationsByCategorie(int $categ_id): array;
    public function getPrestationsByCoffret(int $coffret_id): array;
    public function getThemesCoffrets(): array;
    public function getCoffretById(int $id): CoffretType;
    public function getCoffrets(): array;
}