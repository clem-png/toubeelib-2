<?php

namespace toubeelib_patient\core\repositoryInterfaces;

use toubeelib_patient\core\domain\entities\patient\Patient;

interface PatientRepositoryInterface
{
    public function save(Patient $patient): string;

    public function getPatient(string $id): Patient;
}