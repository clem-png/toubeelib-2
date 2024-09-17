<?php

namespace toubeelib\core\dto;



use toubeelib\core\domain\entities\rdv\Rdv;

class RdvDTO extends DTO
{
    protected string $id;
    protected string $idPatient;
    protected string $idPraticien;
    protected \DateTimeImmutable $dateDebut;
    protected string $specialite_label;
    protected string $status;

    public function __construct(Rdv $r)
    {
        $this->id = $r->getID();
        $this->idPraticien = $r->idPraticien;
        $this->idPatient = $r->idPatient;
        $this->dateDebut = $r->dateDebut;
        $this->specialite_label = $r->specialite->label??'';
        $this->status = $r->status;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'idPatient' => $this->idPatient,
            'idPraticien' => $this->idPraticien,
            'dateDebut' => $this->dateDebut->format('Y-m-d H:i:s'),
            'specialite_label' => $this->specialite_label,
            'status' => $this->status
        ];
    }

}