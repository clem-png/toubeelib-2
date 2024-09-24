<?php

namespace toubeelib\core\dto;

use DateTime;
use DateTimeImmutable;
use toubeelib\core\domain\entities\praticien\Specialite;

class InputRdvDTO extends DTO
{
    protected string $idPatient;
    protected string $idPraticien;
    protected DateTimeImmutable $dateDebut;
    protected ?Specialite $specialite = null;
    protected string $status;


    public function __construct(string $idPraticien, string $idPatient, DateTimeImmutable $dateDebut, string $status) {
        $this->idPatient = $idPatient;
        $this->idPraticien = $idPraticien;
        $this->dateDebut = $dateDebut;
        $this->status = $status;
    }

    public function __get(string $name): mixed{
        return parent::__get($name);
    }

}