<?php

namespace toubeelib_auth\core\services\user;

use toubeelib_auth\core\dto\AuthDTO;
use toubeelib_auth\core\dto\InputAuthDTO;

interface UserServiceInterface
{
    public function findUserById(string $ID): AuthDTO;
    public function createUser(InputAuthDTO $input): void;
}