<?php

namespace toubeelib_auth\core\domain\entities\user;

use toubeelib_auth\core\domain\entities\Entity;
use toubeelib_auth\core\dto\UserDTO;

class User extends Entity
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

    public function toDTO(): UserDTO
    {
        return new UserDTO($this->ID, $this->email, $this->role);
    }
}