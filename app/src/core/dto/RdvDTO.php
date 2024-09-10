<?php

namespace toubeelib\core\dto;



use toubeelib\core\domain\entities\rdv\Rdv;

class RdvDTO extends DTO
{
    protected string $id;
    protected string $idPatient;
    protected string $idPraticien;
    protected string $dateDeb;
    protected string $dateFin;
    protected string $specialite_label;
    protected string $status;

    public function __construct(Rdv $r)
    {
        $this->id = $r->getID();
        $this->idPraticien = $r->idPraticien;
        $this->idPatient = $r->idPatient;
        $this->dateDeb = $r->dateDebut->format('Y-m-d H:i') . PHP_EOL;
        $this->dateFin = $r->dateFin->format('Y-m-d H:i') . PHP_EOL;
        $this->specialite_label = $r->specialite->label;
    }


}