<?php

namespace toubeelib\core\dto;

use DateTime;
use toubeelib\core\domain\entities\praticien\Specialite;

class InputRdvDTO extends DTO
{
    protected string $idPatient;
    protected string $idPraticient;
    protected DateTime $dateDebut;
    protected DateTime $dateFin;
    protected Specialite $specialite;
    protected string $status;


    public function __construct(string $idPatient, string $idPraticient, DateTime $dateDebut, DateTime $dateFin, Specialite $spe, string $status) {
        $this->idPatient = $idPatient;
        $this->idPraticient = $idPraticient;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->specialite = $spe;
        $this->status = $status;
    }

}