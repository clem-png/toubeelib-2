<?php

namespace toubeelib_rdv\core\services\praticien;

use toubeelib_rdv\core\domain\entities\patient\Patient;
use toubeelib_rdv\core\domain\entities\praticien\Specialite;
use toubeelib_rdv\core\domain\entities\praticien\Praticien;
use toubeelib_rdv\core\dto\InputPraticienDTO;
use toubeelib_rdv\core\dto\PraticienDTO;
use toubeelib_rdv\core\dto\SpecialiteDTO;
use toubeelib_rdv\core\dto\InputSearchDTO;

interface ServicePraticienInterface
{
    public function getPraticienById(string $id): Praticien;
    public function getSpecialiteById(string $id): Specialite;
}