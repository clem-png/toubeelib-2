<?php

namespace toubeelib_auth\infrastructure\repositories;

use PDO;
use toubeelib_auth\core\repositoryInterfaces\AuthRepositoryInterface;
use toubeelib_auth\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib_auth\core\domain\entities\auth\Auth;

class PDOAuthRepository implements AuthRepositoryInterface
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    function findByEmail(string $email):Auth{
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            return new Auth(
            $row['id'],
            $row['email'],
            $row['password'],
            $row['role']
            );
        } else {
            throw new RepositoryEntityNotFoundException("Utilisateur non trouvé");
        }
    }
}