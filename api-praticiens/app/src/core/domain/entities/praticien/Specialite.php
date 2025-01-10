<?php

namespace toubeelib_praticiens\core\domain\entities\praticien;

use toubeelib_praticiens\core\domain\entities\Entity;
use toubeelib_praticiens\core\dto\SpecialiteDTO;

class Specialite extends Entity
{

    protected string $label;
    protected string $description;

    public function __construct(string $ID, string $label, string $description)
    {
        $this->ID = $ID;
        $this->label = $label;
        $this->description = $description;
    }

    public function toDTO(): SpecialiteDTO
    {
        return new SpecialiteDTO($this->ID, $this->label, $this->description);

    }
}