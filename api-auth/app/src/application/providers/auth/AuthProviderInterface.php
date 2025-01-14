<?php
namespace toubeelib_auth\application\providers\auth;

use toubeelib_auth\core\dto\AuthDTO;
use toubeelib_auth\core\dto\InputAuthDTO;

interface AuthProviderInterface
{
    public function signIn(InputAuthDTO $credentials): AuthDTO;

    public function getSignIn(string $token): AuthDTO;
}