<?php
namespace gift\appli\core\application\usecases;
Interface CatalogueInterface{
    public function getCategories(): array;
    public function getCategorieById(int $id): array;
    public function getPrestationById(int $id): array;
    public function getPrestationsByCategorie(int $categ_id): array;
    public function getThemesCoffrets(): array;
    public function getCoffretById(int $id): array;
}