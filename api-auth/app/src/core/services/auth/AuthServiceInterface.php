<?php

namespace toubeelib_auth\core\services\auth;

use toubeelib_auth\core\dto\AuthDTO;
use toubeelib_auth\core\dto\InputAuthDTO;

interface AuthServiceInterface
{
    function verifyCredentials(InputAuthDTO $input): AuthDTO;
}