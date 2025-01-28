<?php

namespace toubeelib_rdv\core\services\patient;

use toubeelib_rdv\core\domain\entities\patient\Patient;

interface ServicePatientInterface
{
    public function getPatientById(string $id): Patient;
}