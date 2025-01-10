<?php

namespace toubeelib_rdv\core\services\praticien;

use toubeelib_rdv\core\dto\InputPraticienDTO;
use toubeelib_rdv\core\dto\PraticienDTO;
use toubeelib_rdv\core\dto\SpecialiteDTO;
use toubeelib_rdv\core\dto\InputSearchDTO;

interface ServicePraticienInterface
{

    public function createPraticien(InputPraticienDTO $p): PraticienDTO;
    public function getPraticienById(string $id): PraticienDTO;
    public function getSpecialiteById(string $id): SpecialiteDTO;
    public function getPraticienByTel(string $tel): PraticienDTO;
    public function searchPraticiens(InputSearchDTO $input): array;
}