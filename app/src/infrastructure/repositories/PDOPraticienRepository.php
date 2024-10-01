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
        //bind param
        $stmt->bindParam(1, $id, PDO::PARAM_STR);
        $specialite = $stmt->fetch();
        return new Specialite($specialite['ID'], $specialite['label'], $specialite['description']);
    }

    public function save(Praticien $praticien): string
    {
        $ID = Uuid::uuid4()->toString();
        $praticien->setID($ID);
        $nom = $praticien->nom;
        $prenom = $praticien->prenom;
        $adresse = $praticien->adresse;
        $tel = $praticien->tel;
        $specialite = $praticien->specialite->ID;
        $stmt = $this->pdo->prepare('INSERT INTO praticien (ID, nom, prenom, adresse, tel, specialite) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bindParam(1, $ID, PDO::PARAM_STR);
        $stmt->bindParam(2, $nom, PDO::PARAM_STR);
        $stmt->bindParam(3, $prenom, PDO::PARAM_STR);
        $stmt->bindParam(4, $adresse, PDO::PARAM_STR);
        $stmt->bindParam(5, $tel, PDO::PARAM_STR);
        $stmt->bindParam(6, $specialite, PDO::PARAM_STR);
        $stmt->execute();
        return $ID;
    }

    public function getPraticienById(string $id): Praticien
    {
        $stmt = $this->pdo->prepare('SELECT * FROM praticien WHERE ID = ?');
        $stmt->bindParam(1, $id, PDO::PARAM_STR);
        $praticien = $stmt->fetch();
        $specialite = $this->getSpecialiteById($praticien['specialite']);
        $praticien = new Praticien($praticien['nom'], $praticien['prenom'], $praticien['adresse'], $praticien['tel']);
        $praticien->setSpecialite($specialite);
        return $praticien;
    }

    public function getPraticienByTel(string $tel): Praticien
    {
        $stmt = $this->pdo->prepare('SELECT * FROM praticien WHERE tel = ?');
        $stmt->bindParam(1, $tel, PDO::PARAM_STR);
        $praticien = $stmt->fetch();
        $specialite = $this->getSpecialiteById($praticien['specialite']);
        $praticien = new Praticien($praticien['nom'], $praticien['prenom'], $praticien['adresse'], $praticien['tel']);
        $praticien->setSpecialite($specialite);
        return $praticien;
    }
}