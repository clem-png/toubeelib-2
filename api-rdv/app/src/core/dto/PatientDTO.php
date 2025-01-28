<?php

namespace toubeelib_rdv\core\dto;

use toubeelib_rdv\core\domain\entities\patient\Patient;

class PatientDTO extends DTO
{
    protected string $ID;
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $mail;
    protected string $dateNaissance;
    protected string $numSecu;

    protected string $numeroTel;

    public function __construct(Patient $p)
    {
        $this->ID = $p->getID();
        $this->nom = $p->nom;
        $this->prenom = $p->prenom;
        $this->adresse = $p->adresse;
        $this->mail = $p->mail;
        $this->dateNaissance = $p->dateNaissance;
        $this->numSecu = $p->numSecu;
        $this->numeroTel = $p->numeroTel;
    }
}