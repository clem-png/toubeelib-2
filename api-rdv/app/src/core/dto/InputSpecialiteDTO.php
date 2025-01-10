<?php

namespace toubeelib_rdv\core\dto;

use Exception;

class InputSpecialiteDTO extends DTO
{
    protected string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @throws Exception
     */
    public function __get(string $name): mixed{
        return parent::__get($name);
    }
}