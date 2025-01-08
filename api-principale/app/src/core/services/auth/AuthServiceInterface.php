<?php

namespace toubeelib\core\services\auth;

use toubeelib\core\dto\AuthDTO;
use toubeelib\core\dto\InputAuthDTO;

interface AuthServiceInterface
{
    function verifyCredentials(InputAuthDTO $input): AuthDTO;
}