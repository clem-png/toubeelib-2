<?php

namespace toubeelib\core\dto;

use DateTimeImmutable;

class InputRdvDTO extends DTO
{
    protected string $idPatient;
    protected string $idPraticien;
    protected DateTimeImmutable $dateDebut;
    protected ?InputSpecialiteDTO $specialite;


    public function __construct(string $idPraticien, string $idPatient, DateTimeImmutable $dateDebut, ?InputSpecialiteDTO $specialite = null) {
        $this->idPatient = $idPatient;
        $this->idPraticien = $idPraticien;
        $this->dateDebut = $dateDebut;
        $this->specialite = $specialite;
    }

    public function __get(string $name): mixed{
        return parent::__get($name);
    }

}