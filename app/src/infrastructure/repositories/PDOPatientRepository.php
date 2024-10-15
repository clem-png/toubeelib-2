<?php

namespace toubeelib\infrastructure\repositories;

use Exception;
use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\patient\Patient;
use toubeelib\core\repositoryInterfaces\PatientRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class PDOPatientRepository implements PatientRepositoryInterface
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @throws RepositoryEntityNotFoundException
     */
    public function save(Patient $patient): string
    {
        try {
            $ID = Uuid::uuid4()->toString();
            $nom = $patient->nom;
            $prenom = $patient->prenom;
            $adresse = $patient->adresse;
            $dateNaissance = $patient->dateNaissance->format('Y-m-d');
            $mail = $patient->mail;
            $numSecu = $patient->numSecu;
            $stmt = $this->pdo->prepare('INSERT INTO patient (id, "num_secu", "date_naissance", nom, prenom, adresse, mail) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bindParam(1, $ID);
            $stmt->bindParam(2, $numSecu);
            $stmt->bindParam(3, $dateNaissance);
            $stmt->bindParam(4, $nom);
            $stmt->bindParam(5, $prenom);
            $stmt->bindParam(6, $adresse);
            $stmt->bindParam(7, $mail);
            $stmt->execute();

            $tel[] = $patient->numerosTel;
            foreach ($tel as $num) {
                // vérifier si le numéro est déjà dans la base
                $stmt = $this->pdo->prepare('SELECT * FROM "num_patient" WHERE numero = ?');
                $stmt->bindParam(1, $num);
                $stmt->execute();
                $num = $stmt->fetch();
                if ($num != null) {
                    throw new RepositoryEntityNotFoundException('Le numéro de téléphone {$num} est déjà utilisé');

                }
                $stmt = $this->pdo->prepare('INSERT INTO "num_patient" ("idPatient", numero) VALUES (?, ?)');
                $stmt->bindParam(1, $ID);
                $stmt->bindParam(2, $num);
                $stmt->execute();
            }
        } catch (Exception $e) {
            throw new RepositoryEntityNotFoundException($e->getMessage());
        }
        return $ID;

    }
}