<?php

namespace toubeelib\core\domain\entities\rdv;

use DateTime;
use DateTimeImmutable;
use toubeelib\core\domain\entities\Entity;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\dto\RdvDTO;

class Rdv extends Entity
{
    protected string $idPatient;
    protected string $idPraticien;
    protected DateTimeImmutable $dateDebut;

    protected ?Specialite $specialite = null;
    protected string $status;

    public function __construct(string $idPraticien, string $idPatient, string $status, DateTimeImmutable $dateDebut)
    {
        $this->idPatient = $idPatient;
        $this->idPraticien = $idPraticien;
        $this->dateDebut = $dateDebut;
        $this->status = $status;
    }

    public function toDTO(): RdvDTO
    {
        return new RdvDTO($this);
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setPatientId(string $id): void
    {
        $this->idPatient = $id;
    }

    public function setSpecialite(Specialite $specialite): void
    {
        $this->specialite = $specialite;
    }
}