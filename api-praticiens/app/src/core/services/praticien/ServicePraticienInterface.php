<?php

namespace toubeelib_praticiens\core\services\praticien;

use toubeelib_praticiens\core\dto\InputPraticienDTO;
use toubeelib_praticiens\core\dto\PraticienDTO;
use toubeelib_praticiens\core\dto\SpecialiteDTO;
use toubeelib_praticiens\core\dto\InputSearchDTO;

interface ServicePraticienInterface
{

    public function createPraticien(InputPraticienDTO $p): PraticienDTO;
    public function getPraticienById(string $id): PraticienDTO;
    public function getSpecialiteById(string $id): SpecialiteDTO;
    public function getPraticienByTel(string $tel): PraticienDTO;
    public function searchPraticiens(InputSearchDTO $input): array;


}