<?php

namespace toubeelib_praticiens\core\repositoryInterfaces;

use toubeelib_praticiens\core\domain\entities\praticien\Praticien;
use toubeelib_praticiens\core\domain\entities\praticien\Specialite;
use toubeelib_praticiens\core\dto\InputSearchDTO;

interface PraticienRepositoryInterface
{

    public function getSpecialiteById(string $id): Specialite;
    public function save(Praticien $praticien): string;
    public function getPraticienById(string $id): Praticien;
    public function getPraticienByTel(string $tel): Praticien;
    public function searchPraticiens(InputSearchDTO $input): array;
    public function existPraticienByTel(string $tel): bool;

}