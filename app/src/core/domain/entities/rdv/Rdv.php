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

    public function __construct(string $idPatient, string $idPraticien, string $status, DateTimeImmutable $dateDebut)
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
}