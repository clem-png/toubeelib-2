<?php

namespace toubeelib_auth\infrastructure\repositories;

use PDO;
use toubeelib_auth\core\repositoryInterfaces\UserRepositoryInterface;
use toubeelib_auth\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib_auth\core\domain\entities\user\User;

class PDOUserRepository implements UserRepositoryInterface
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    function findByEmail(string $email):User | null{
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            $auth = new User(
            $row['email'],
            $row['password'],
            intval($row['role'])
            );
            $auth->setID($row['id']);
            return $auth;

        } else {
            return null;
        }
    }

    function findById(string $id):User{
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            $auth = new User(
            $row['email'],
            $row['password'],
            $row['role']
            );
            $auth->setID($row['id']);
            return $auth;
        } else {
            throw new RepositoryEntityNotFoundException("Utilisateur non trouvé");
        }
    }

    function save(User $auth): string{
        $email = $auth->email;
        $password = $auth->password;
        $role = $auth->role;
        try {
            $stmt = $this->pdo->prepare('INSERT INTO users (email, password, role) VALUES (?, ?, ?) RETURNING id');
            $stmt->bindParam(1, $email);
            $stmt->bindParam(2, $password);
            $stmt->bindParam(3, $role);
            $stmt->execute();
            $id = $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new RepositoryEntityNotFoundException($e->getMessage());
        }

        return $id;
    }
}