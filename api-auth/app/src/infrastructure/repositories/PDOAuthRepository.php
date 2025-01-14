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

    function findById(string $id):Auth{
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            $auth = new Auth(
            $row['email'],
            $row['password'],
            $row['role']
            );
            $auth->setID($row['id']);
        } else {
            throw new RepositoryEntityNotFoundException("Utilisateur non trouvé");
        }
    }

    function save(Auth $auth): string{
        $email = $auth->email;
        $password = $auth->password;
        $role = $auth->role;
        try {
            $stmt = $this->pdo->prepare('INSERT INTO users (email, password, role) VALUES (?, ?, ?)');
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $password);
            $stmt->bindParam(4, $role);
            $stmt->execute();
            $id = $this->pdo->lastInsertId();
        } catch (Exception $e) {
            throw new RepositoryEntityNotFoundException($e->getMessage());
        }

        return $id;
    }
}