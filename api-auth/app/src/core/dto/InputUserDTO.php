<?php

namespace toubeelib_auth\core\dto;
class InputUserDTO extends DTO
{
    protected string $email;
    protected string $password;

    public function __construct(string $email, string $password) {
        $this->email = $email;
        $this->password = $password;
    }

}