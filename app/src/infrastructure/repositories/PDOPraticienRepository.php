<?php

namespace toubeelib\infrastructure\repositories;

use PDO;
use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\dto\InputSearchDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class PDOPraticienRepository implements PraticienRepositoryInterface
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getSpecialiteById(string $id): Specialite
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM specialite WHERE ID = ?');
            $stmt->bindParam(1, $id, PDO::PARAM_STR);
            $stmt->execute();
            $specialite = $stmt->fetch();
            if (!$specialite) {
                throw new RepositoryEntityNotFoundException("Specialite not found");
            }
            return new Specialite($specialite['id'], $specialite['label'], $specialite['desc']);
        } catch (\Exception $e) {
            throw new RepositoryEntityNotFoundException("Error fetching specialite: " . $e->getMessage());
        }
    }

    public function save(Praticien $praticien): string
    {
        try {
            $ID = Uuid::uuid4()->toString();
            $nom = $praticien->nom;
            $prenom = $praticien->prenom;
            $adresse = $praticien->adresse;
            $tel = $praticien->tel;
            $specialite = $praticien->specialite->ID;
            $stmt = $this->pdo->prepare('INSERT INTO praticien (ID, nom, prenom, adresse, tel) VALUES (?, ?, ?, ?, ?)');
            $stmt->bindParam(1, $ID, PDO::PARAM_STR);
            $stmt->bindParam(2, $nom, PDO::PARAM_STR);
            $stmt->bindParam(3, $prenom, PDO::PARAM_STR);
            $stmt->bindParam(4, $adresse, PDO::PARAM_STR);
            $stmt->bindParam(5, $tel, PDO::PARAM_STR);
            $stmt->execute();
            $stmt = $this->pdo->prepare('INSERT INTO praticien_spe ("idPraticien", "idSpe") VALUES (?, ?)');
            $stmt->bindParam(1, $ID, PDO::PARAM_STR);
            $stmt->bindParam(2, $specialite, PDO::PARAM_STR);
            $stmt->execute();
            return $ID;
        } catch (\Exception $e) {
            throw new RepositoryEntityNotFoundException("Error saving praticien: " . $e->getMessage());
        }
    }

    public function getPraticienById(string $id): Praticien
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM praticien inner join praticien_spe on praticien.id = praticien_spe."idPraticien" WHERE praticien.id = ?');
            $stmt->bindParam(1, $id, PDO::PARAM_STR);
            $stmt->execute();
            $praticienRes = $stmt->fetch();
            if (!$praticienRes) {
                throw new RepositoryEntityNotFoundException("Praticien not found");
            }
            $specialite = $this->getSpecialiteById($praticienRes['idSpe']);
            $praticien = new Praticien($praticienRes['nom'], $praticienRes['prenom'], $praticienRes['adresse'], $praticienRes['tel']);
            $praticien->setID($praticienRes['id']);
            $praticien->setSpecialite($specialite);
            return $praticien;
        } catch (\Exception $e) {
            throw new RepositoryEntityNotFoundException("Error fetching praticien: " . $e->getMessage());
        }
    }

    public function getPraticienByTel(string $tel): Praticien
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM praticien  inner join praticien_spe on praticien.id = praticien_spe."idPraticien" WHERE tel = ?');
            $stmt->bindParam(1, $tel, PDO::PARAM_STR);
            $stmt->execute();
            $praticienRes = $stmt->fetch();
            if (!$praticienRes) {
                throw new RepositoryEntityNotFoundException("Praticien not found");
            }
            $specialite = $this->getSpecialiteById($praticienRes['idSpe']);
            $praticien = new Praticien($praticienRes['nom'], $praticienRes['prenom'], $praticienRes['adresse'], $praticienRes['tel']);
            $praticien->setID($praticienRes['id']);
            $praticien->setSpecialite($specialite);
            return $praticien;
        } catch (\Exception $e) {
            throw new RepositoryEntityNotFoundException("Error fetching praticien: " . $e->getMessage());
        }
    }

    public function searchPraticiens(InputSearchDTO $input): array
    {
        try {
            $query = 'SELECT * FROM praticien INNER JOIN praticien_spe ON praticien.id = praticien_spe."idPraticien" WHERE 1=1';
            $params = [];

            if ($input->prenom !== null) {
                $query .= ' AND prenom LIKE :prenom';
                $params[':prenom'] = '%' . $input->prenom . '%';
            }

            if ($input->nom !== null) {
                $query .= ' AND nom LIKE :nom';
                $params[':nom'] = '%' . $input->nom . '%';
            }

            if ($input->tel !== null) {
                $query .= ' AND tel LIKE :tel';
                $params[':tel'] = '%' . $input->tel . '%';
            }

            if ($input->adresse !== null) {
                $query .= ' AND adresse LIKE :adresse';
                $params[':adresse'] = '%' . $input->adresse . '%';
            }

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll();

            $praticiens = [];
            foreach ($results as $praticienRes) {
                $specialite = $this->getSpecialiteById($praticienRes['idSpe']);
                $praticien = new Praticien($praticienRes['nom'], $praticienRes['prenom'], $praticienRes['adresse'], $praticienRes['tel']);
                $praticien->setID($praticienRes['id']);
                $praticien->setSpecialite($specialite);
                $praticiens[] = $praticien;
            }

            return $praticiens;
        } catch (\Exception $e) {
            throw new RepositoryEntityNotFoundException("Error searching praticiens: " . $e->getMessage());
        }
    }

    public function existPraticienByTel(string $tel): bool
    {
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM praticien WHERE tel = ?');
            $stmt->bindParam(1, $tel, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            throw new RepositoryEntityNotFoundException("Error checking praticien existence: " . $e->getMessage());
        }
    }
}