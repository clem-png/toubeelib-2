<?php

namespace toubeelib\core\dto;

class InputSearchDTO extends DTO
{
    protected ?string $nom;
    protected ?string $prenom;
    protected ?string $adresse;
    protected ?string $tel;

    public function __construct(?string $nom = null, ?string $prenom = null, ?string $adresse = null, ?string $tel = null) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->tel = $tel;
    }
}
