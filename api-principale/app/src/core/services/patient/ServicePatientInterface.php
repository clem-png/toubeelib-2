<?php

namespace toubeelib\core\services\patient;

use toubeelib\core\dto\InputPatientDTO;
use toubeelib\core\dto\PatientDTO;

interface ServicePatientInterface
{
    public function creerPatient(InputPatientDTO $DTO): PatientDTO;
}