<?php

namespace toubeelib_rdv\core\services\rdv;

use toubeelib_rdv\core\dto\InputRdvDTO;
use toubeelib_rdv\core\dto\PatientDTO;
use toubeelib_rdv\core\dto\PraticienDTO;
use toubeelib_rdv\core\dto\RdvDTO;
use toubeelib_rdv\core\dto\InputSpecialiteDTO;
use DateTime;

interface ServiceRDVInterface{

    public function listerDisponibilitePraticien(DateTime $dateDebut, DateTime $dateFin, string $id): array;
    public function consulterRdv(string $rdv_id): RdvDTO;
    public function creerRdv(InputRdvDTO $DTO): RdvDTO;
    public function annulerRdv(string $rdv_id):RdvDTO;
    public function modifierPatientOuSpecialiteRdv(String $rdv_id, String $patient_id, ?InputSpecialiteDTO $specialite): RdvDTO;
    public function marquerRdvHonore(string $rdv_id): RdvDTO;
    public function marquerRdvNonHonore(string $rdv_id): RdvDTO;
    public function listerRdvPatient(string $patient_id): array;
    public function afficherPlanningPraticien(DateTime $dateDebut, DateTime $dateFin, string $id, string $idSpe, string $type): array;
    public function indisponibilitePraticien(DateTime $dateDebut, DateTime $dateFin, string $id): void ;
    public function createMessage(array $rdv, string $action) : array;
}
