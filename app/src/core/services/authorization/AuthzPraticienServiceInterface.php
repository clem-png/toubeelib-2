<?php

namespace toubeelib\core\services\authorization;

use Ramsey\Uuid\Uuid;

interface AuthzPraticienServiceInterface
{
    function isGranted(Uuid $user_id, int $role, int $operation, Uuid $ressource_id): bool;
}