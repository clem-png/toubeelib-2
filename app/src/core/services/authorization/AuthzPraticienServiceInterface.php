<?php

namespace toubeelib\core\services\authorization;

use Ramsey\Uuid\Uuid;

interface AuthzPraticienServiceInterface
{
    function isGranted(string $user_id, int $role, int $operation, string $ressource_id): bool;
}