<?php

namespace toubeelib\core\domain\entities\rdv;

use DateTime;
use toubeelib\core\domain\entities\Entity;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\dto\RdvDTO;

class Rdv extends Entity
{
    protected string $idPatient;
    protected string $idPraticient;
    protected DateTime $dateDebut;
    protected DateTime $dateFin;
    protected Specialite $specialite;
    protected string $status;

    public function __construct(string $idPatient, string $idPraticient, string $status, DateTime $dateDebut, DateTime $dateFin, Specialite $spe)
    {
        $this->idPatient = $idPatient;
        $this->idPraticient = $idPraticient;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->specialite = $spe;
        $this->status = $status;
    }

    public function toDTO(): RdvDTO
    {
        return new RdvDTO($this);
    }
}