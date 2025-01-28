<?php

namespace toubeelib_patient\core\services\patient;

use toubeelib_patient\core\dto\InputPatientDTO;
use toubeelib_patient\core\dto\PatientDTO;

interface ServicePatientInterface
{
    public function creerPatient(InputPatientDTO $DTO): PatientDTO;

    public function getPatientById(string $id): PatientDTO;
}