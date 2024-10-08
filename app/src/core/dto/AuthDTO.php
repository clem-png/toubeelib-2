<?php

namespace toubeelib\core\dto;

class AuthDTO extends DTO
{
    protected string $id;
    protected string $email;
    protected int $role;

    public function __construct(string $id, string $email, int $role) {
        $this->id = $id;
        $this->email = $email;
        $this->role = $role;
    }

}