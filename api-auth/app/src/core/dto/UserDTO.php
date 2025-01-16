<?php

namespace toubeelib_auth\core\dto;

class UserDTO extends DTO
{
    protected string $id;
    protected string $email;
    protected int $role;
    protected ?string $accessToken;
    protected ?string $refreshToken;

    public function __construct(string $id, string $email, int $role, ?string $accessToken = null, ?string $refreshToken = null) {
        $this->id = $id;
        $this->email = $email;
        $this->role = $role;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }
}