<?php
namespace toubeelib_auth\application\providers\auth;

use toubeelib_auth\core\dto\UserDTO;
use toubeelib_auth\core\dto\InputUserDTO;

interface AuthProviderInterface
{
    public function signIn(InputUserDTO $credentials): UserDTO;

    public function getSignIn(string $token): UserDTO;
}