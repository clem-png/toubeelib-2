<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;

interface PraticienRepositoryInterface
{

    public function getSpecialiteById(string $id): Specialite;
    public function save(Praticien $praticien): string;
    public function getPraticienById(string $id): Praticien;
    public function getPraticienByTel(string $tel): Praticien;
    public function searchPraticiens(?string $prenom, ?string $nom, ?string $tel, ?string $adresse): array;
    public function existPraticienByTel(string $tel): bool;

}