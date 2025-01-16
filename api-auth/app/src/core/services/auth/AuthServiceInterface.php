<?php

namespace toubeelib_auth\core\services\auth;

use toubeelib_auth\core\dto\UserDTO;
use toubeelib_auth\core\dto\InputUserDTO;

interface AuthServiceInterface
{
    function verifyCredentials(InputUserDTO $input): UserDTO;
}