<?php

namespace toubeelib\infrastructure\repositories;

use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class PDORdvRepository implements RdvRepositoryInterface
{

    private \PDO $pdo;


    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRdvById(string $id): Rdv
    {
        $stmt = $this->pdo->prepare('SELECT * FROM rdv WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $rdv = $stmt->fetch();
        if (!$rdv) {
            throw new RepositoryEntityNotFoundException('Rdv not found');
        }
        return new Rdv($rdv['idPraticien'], $rdv['idPatient'], $rdv['status'], $rdv['dateDebut']);
    }

    public function save(Rdv $rdv): string
    {
        $ID = Uuid::uuid4()->toString();
        $idPraticien = $rdv->idPraticien;
        $idPatient = $rdv->idPatient;
        $status = $rdv->status;
        $dateDebut = $rdv->dateDebut->format('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare('INSERT INTO rdv (id, idPraticien, idPatient, status, dateDebut) VALUES (:id, :idPraticien, :idPatient, :status, :dateDebut)');
        $stmt->bindParam(':id', $ID);
        $stmt->bindParam(':idPraticien', $idPraticien);
        $stmt->bindParam(':idPatient', $idPatient);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->execute();
        return $ID;
    }

    public function getRdvByPatientId(string $id): array
    {
        $rdvs = [];
        $stmt = $this->pdo->prepare('SELECT * FROM rdv');
        $stmt->execute();
        $rdvsRes = $stmt->fetchAll();
        foreach ($rdvsRes as $rdv) {
            if ($rdv['idPatient'] === $id) {
                $rdvs[] = new Rdv($rdv['idPraticien'], $rdv['idPatient'], $rdv['status'], $rdv['dateDebut']);
            }
        }
        return $rdvs;
    }

    public function update(Rdv $rdv): void
    {
        //transforme en pdo
        $ID = $rdv->getID();
        $stmt = $this->pdo->prepare('Select * from rdv where id = ?');
        $stmt->bindParam('1', $ID);
        $rdv = $stmt->fetch();

        if (!$rdv) {
            throw new RepositoryEntityNotFoundException('Rdv not found');
        }

        $idPraticien = $rdv->idPraticien;
        $idPatient = $rdv->idPatient;
        $status = $rdv->status;
        $dateDebut = $rdv->dateDebut->format('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare('UPDATE rdv SET idPraticien = ?, idPatient = ?, status = ?, dateDebut = ? WHERE id = ?');
        $stmt->bindParam(1, $idPraticien);
        $stmt->bindParam(2, $idPatient);
        $stmt->bindParam(3, $status);
        $stmt->bindParam(4, $dateDebut);
        $stmt->bindParam(5, $ID);
        $stmt->execute();
    }

    public function getRdvByPraticienId(string $id): array
    {
        $rdvs = [];
        $stmt = $this->pdo->prepare('SELECT * FROM rdv');
        $stmt->execute();
        $rdvsRes = $stmt->fetchAll();
        foreach ($rdvsRes as $rdv) {
            if ($rdv['idPraticien'] === $id) {
                $rdvs[] = new Rdv($rdv['idPraticien'], $rdv['idPatient'], $rdv['status'], $rdv['dateDebut']);
            }
        }
        return $rdvs;
    }
}