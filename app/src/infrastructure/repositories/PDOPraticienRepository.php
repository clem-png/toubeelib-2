<?php

namespace toubeelib\infrastructure\repositories;

use PDO;
use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;

class PDOPraticienRepository implements PraticienRepositoryInterface
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getSpecialiteById(string $id): Specialite
    {
        $stmt = $this->pdo->prepare('SELECT * FROM specialite WHERE ID = ?');
        $stmt->bindParam(1, $id, PDO::PARAM_STR);
        $stmt->execute();
        $specialite = $stmt->fetch();
        return new Specialite($specialite['id'], $specialite['label'], $specialite['desc']);
    }

    public function save(Praticien $praticien): string
    {
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
    }

    public function getPraticienById(string $id): Praticien
    {
        $stmt = $this->pdo->prepare('SELECT * FROM praticien inner join praticien_spe on praticien.id = praticien_spe."idPraticien" WHERE praticien.id = ?');
        $stmt->bindParam(1, $id, PDO::PARAM_STR);
        $stmt->execute();
        $praticienRes = $stmt->fetch();
        $specialite = $this->getSpecialiteById($praticienRes['idSpe']);
        $praticien = new Praticien($praticienRes['nom'], $praticienRes['prenom'], $praticienRes['adresse'], $praticienRes['tel']);
        $praticien->setID($praticienRes['id']);
        $praticien->setSpecialite($specialite);
        return $praticien;
    }

    public function getPraticienByTel(string $tel): Praticien
    {
        $stmt = $this->pdo->prepare('SELECT * FROM praticien  inner join praticien_spe on praticien.id = praticien_spe."idPraticien" WHERE tel = ?');
        $stmt->bindParam(1, $tel, PDO::PARAM_STR);
        $stmt->execute();
        $praticienRes = $stmt->fetch();
        $specialite = $this->getSpecialiteById($praticienRes['idSpe']);
        $praticien = new Praticien($praticienRes['nom'], $praticienRes['prenom'], $praticienRes['adresse'], $praticienRes['tel']);
        $praticien->setID($praticienRes['id']);
        $praticien->setSpecialite($specialite);
        return $praticien;
    }

    public function getAllPraticiens(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM praticien inner join praticien_spe on praticien.id = praticien_spe."idPraticien"');
        $stmt->execute();
        $praticiens = $stmt->fetchAll();
        $praticiensRes = [];
        foreach ($praticiens as $praticien) {
            $specialite = $this->getSpecialiteById($praticien['idSpe']);
            $praticiensRes[] = new Praticien($praticien['nom'], $praticien['prenom'], $praticien['adresse'], $praticien['tel'], $specialite);
        }
        return $praticiensRes;
    }
}