<?php

namespace toubeelib_rdv\core\services\praticien;

use toubeelib_rdv\core\dto\InputPraticienDTO;
use toubeelib_rdv\core\dto\PraticienDTO;
use toubeelib_rdv\core\dto\SpecialiteDTO;
use toubeelib_rdv\core\dto\InputSearchDTO;

interface ServicePraticienInterface
{
    public function getPraticienById(string $id): PraticienDTO;
    public function getSpecialiteById(string $id): SpecialiteDTO;
}