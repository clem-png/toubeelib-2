<?php

namespace toubeelib\core\dto;

use Respect\Validation\Rules\Date;
use toubeelib\core\domain\entities\patient\Patient;
use toubeelib\core\dto\DTO;

class PatientDTO extends DTO
{
    protected string $ID;
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected string $mail;
    protected Date $dateNaissance;
    protected string $numSecu;

    protected [] $numerosTel;

    public function __construct(Patient $p)
    {
        $this->ID = $p->getID();
        $this->nom = $p->nom;
        $this->prenom = $p->prenom;
        $this->adresse = $p->adresse;
        $this->tel = $p->tel;
        $this->mail = $p->mail;
        $this->dateNaissance = $p->dateNaissance;
        $this->numSecu = $p->numSecu;
        $this->numerosTel = $p->numerosTel;
    }
}