<?php

namespace toubeelib\core\dto;

use toubeelib\core\dto\DTO;

class InputSpecialiteDTO extends DTO
{
    protected string $label;

    public function __construct(string $label)
    {
        $this->label = $label;
    }
}