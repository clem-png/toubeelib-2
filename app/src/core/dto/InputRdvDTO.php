<?php

namespace toubeelib\core\dto;

use DateTime;
use DateTimeImmutable;
use toubeelib\core\domain\entities\praticien\Specialite;

class InputRdvDTO extends DTO
{
    protected string $idPatient;
    protected string $idPraticient;
    protected DateTime $dateDebut;
    protected DateTime $dateFin;
    protected Specialite $specialite;
    protected string $status;


    public function __construct(string $idPatient, string $idPraticient, DateTimeImmutable $dateDebut, string $status) {
        $this->idPatient = $idPatient;
        $this->idPraticient = $idPraticient;
        $this->dateDebut = $dateDebut;
        $this->status = $status;
    }

    public function __get(string $name): mixed{
        return parent::__get($name);
    }

}