<?php

namespace toubeelib_auth\core\repositoryInterfaces;

use toubeelib_auth\core\domain\entities\user\User;

interface UserRepositoryInterface
{
    function findByEmail(string $email):User;
    function save(User $auth): string;
    function findById(string $id):User;
}
