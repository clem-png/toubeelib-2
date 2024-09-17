<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\dto\InputPraticienDTO;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\PraticienDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\dto\SpecialiteDTO;
use DateTime;

interface ServiceRDVInterface{

    public function listerDisponibilitePraticien(DateTime $dateDebut, DateTime $dateFin, string $id);
    public function consulterRdv(string $rdv_id);
    public function creerRdv(InputRdvDTO $DTO): RdvDTO;
    public function annulerRdv(string $rdv_id);
    public function modifierPatientRdv(String $rdv_id, String $patient_id);

}