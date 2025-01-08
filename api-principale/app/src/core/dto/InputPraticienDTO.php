<?php

namespace toubeelib\core\dto;

class InputPraticienDTO extends DTO
{
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected ?InputSpecialiteDTO $specialite;


    public function __construct(string $nom, string $prenom, string $adresse, string $tel, ?InputSpecialiteDTO $specialite = null) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->tel = $tel;
        $this->specialite = $specialite;
    }

}