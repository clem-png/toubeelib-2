<?php

namespace toubeelib_auth\core\services\user;

use toubeelib_auth\core\dto\UserDTO;
use toubeelib_auth\core\dto\InputUserDTO;

interface UserServiceInterface
{
    public function findUserById(string $ID): UserDTO;
    public function createUser(InputUserDTO $input): void;
}