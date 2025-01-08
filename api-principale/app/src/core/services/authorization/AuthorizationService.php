<?php

namespace toubeelib\core\services\authorization;

use Ramsey\Uuid\Uuid;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;

class AuthorizationService implements AuthzPraticienServiceInterface
{
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(PraticienRepositoryInterface $praticienRepository)
    {
        $this->praticienRepository = $praticienRepository;
    }

    function isGranted(string $user_id, int $role, int $operation, string $ressource_id): bool
    {
        $retour = false;

        if ($role === 100) {
            $retour = true;
        }

        if (($role === 5) && ($ressource_id === $user_id)) {
            switch ($operation) {
                case 0: // lire
                    $retour = true;
                    break;
                case 1: // modifier
                    $retour = true;
                    break;
                case 2: // supprimer
                    $retour = false;
                    break;
                default:
                    $retour = false;
                    break;
            }
        }
        return $retour;
    }
}