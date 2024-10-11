<?php
namespace toubeelib\application\providers\auth;

use toubeelib\core\dto\AuthDTO;
use toubeelib\core\dto\InputAuthDTO;

interface AuthProviderInterface
{
    public function signIn(InputAuthDTO $credentials): AuthDTO;
}