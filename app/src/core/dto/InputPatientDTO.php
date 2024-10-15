<?php

namespace toubeelib\core\dto;

use Respect\Validation\Rules\Date;
use toubeelib\core\dto\DTO;

class InputPatientDTO extends DTO
{

    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected string $mail;
    protected Date $dateNaissance;
    protected string $numSecu;

    protected [] $numerosTel;

    public function __construct(string $nom, string $prenom, string $adresse, string $tel, string $mail, string $dateNaissance, string $numSecu, [] $numerosTel = []) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->tel = $tel;
        $this->mail = $mail;
        $this->dateNaissance = $dateNaissance;
        $this->numSecu = $numSecu;
        $this->numerosTel = $numerosTel;
    }
}