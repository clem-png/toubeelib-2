<?php

namespace toubeelib\core\services\praticien;

use toubeelib\core\dto\InputPraticienDTO;
use toubeelib\core\dto\PraticienDTO;
use toubeelib\core\dto\SpecialiteDTO;
use toubeelib\core\dto\InputSearchDTO;

interface ServicePraticienInterface
{

    public function createPraticien(InputPraticienDTO $p): PraticienDTO;
    public function getPraticienById(string $id): PraticienDTO;
    public function getSpecialiteById(string $id): SpecialiteDTO;
    public function getPraticienByTel(string $tel): PraticienDTO;
    public function searchPraticiens(InputSearchDTO $input): array;


}