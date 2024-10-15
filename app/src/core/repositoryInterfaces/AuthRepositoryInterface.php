<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\auth\Auth;

interface AuthRepositoryInterface
{
    function findByEmail(string $email):Auth;}