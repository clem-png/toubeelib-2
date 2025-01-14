<?php

namespace toubeelib_auth\core\domain\entities\auth;

use toubeelib_auth\core\domain\entities\Entity;
use toubeelib_auth\core\dto\AuthDTO;

class Auth extends Entity
{
    protected string $email;
    protected int $role;
    protected string $password;

    public function __construct(string $email, string $password, int $role)
    {
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function toDTO(): AuthDTO
    {
        return new AuthDTO($this->ID, $this->email, $this->role);
    }
}