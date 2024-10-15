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
    protected Date $dateNaissance;
    protected string $numSecu;

    protected [] $numerosTel;

    public function __construct(string $nom, string $prenom, string $adresse, string $mail, Date $dateNaissance, string $numSecu, [] $numerosTel = [], string $numeroTel = null)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->mail = $mail;
        $this->dateNaissance = $dateNaissance;
        $this->numSecu = $numSecu;
        $this->numerosTel = $numerosTel;
        if ($numeroTel !== null) {
            $this->numerosTel[] = $numeroTel;
        }
    }

    public function ajouterNumeroTel(string $numeroTel): void
    {
        $this->numerosTel[] = $numeroTel;
    }

    public function toDTO(): PatientDTO
    {
        return new PatientDTO($this);
    }

}