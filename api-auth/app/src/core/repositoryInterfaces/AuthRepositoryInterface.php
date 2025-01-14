<?php

namespace toubeelib_auth\core\repositoryInterfaces;

use toubeelib_auth\core\domain\entities\auth\Auth;

interface AuthRepositoryInterface
{
    function findByEmail(string $email):Auth;}