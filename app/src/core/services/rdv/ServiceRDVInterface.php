<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\dto\InputPraticienDTO;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\PraticienDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\dto\SpecialiteDTO;

interface ServiceRDVInterface{

    public function listerDisponibilitesPraticien();
    public function consulterRdv(string $rdv_id);
    public function creerRdv(InputRdvDTO $DTO): RdvDTO;
    public function annulerRdv();
    public function modifierPatientRdv();

}