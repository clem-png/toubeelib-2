<?php

namespace toubeelib\core\domain\entities\patient;

use Respect\Validation\Rules\Date;
use toubeelib\core\domain\entities\Entity;
use toubeelib\core\dto\PatientDTO;

class Patient extends Entity
{
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $mail;
    protected string $dateNaissance;
    protected string $numSecu;

    protected string $numeroTel;

    public function __construct(string $nom, string $prenom, string $adresse, string $mail, string $dateNaissance, string $numSecu, string $numeroTel)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->mail = $mail;
        $this->dateNaissance = $dateNaissance;
        $this->numSecu = $numSecu;
        $this->numeroTel = $numeroTel;
    }

    public function toDTO(): PatientDTO
    {
        return new PatientDTO($this);
    }

}