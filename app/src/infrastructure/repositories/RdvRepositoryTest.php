<?php

namespace toubeelib\infrastructure\repositories;

use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\SkippedTestSuiteError;
use PHPUnit\Framework\TestCase;
use toubeelib\core\domain\entities\rdv\Rdv;

class RdvRepositoryTest extends TestCase
{
    private $rdvRepository;
    private $pdo;

    protected function setUp(): void
    {
        try {
            $config = parse_ini_file(__DIR__.'/../config/praticien.db.ini');
            $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['dbname']}";
            $user = $config['username'];
            $password = $config['password'];
            $this->pdo = new \PDO($dsn, $user, $password);
            $this->rdvRepository = new PDORdvRepository($this->pdo);
        }catch (\Exception $e){
            throw new SkippedTestSuiteError("erreur de setup, skip de tous les tests ---- PraticienRepositoryTest");
        }
    }

    public function testGetRdvById()
    {
        $rdv = $this->rdvRepository->getRdvById('r1');
        $this->assertSame('r1', $rdv->getID());
        $this->assertSame('p1', $rdv->idPraticien);
        $this->assertSame('p2', $rdv->idPatient);
        $this->assertSame('status', $rdv->status);
        $this->assertSame('2021-01-01 00:00:00', $rdv->dateDebut);
    }

    public function testSave() {
        $rdv = new Rdv('p1', 'p2', 'status', new DateTimeImmutable('2021-01-01 00:00:00'));
        $id = $this->rdvRepository->save($rdv);
        $this->assertIsString($id);

        $stmt = $this->pdo->prepare('SELECT * FROM rdv WHERE ID = ?');
        $stmt->bindParam(1, $id, PDO::PARAM_STR);
        $stmt->execute();
        $rdvRes = $stmt->fetch();
        $this->assertNotNull($rdvRes);
        $this->assertSame('p1', $rdvRes['idPraticien']);
        $this->assertSame('p2', $rdvRes['idPatient']);
        $this->assertSame('status', $rdvRes['status']);
        $this->assertSame('2021-01-01 00:00:00', $rdvRes['dateDebut']);
    }

    public function testGetRdvByPatientId() {
        $rdvs = $this->rdvRepository->getRdvByPatientId('p2');
        $this->assertIsArray($rdvs);
        $this->assertCount(1, $rdvs);
        $this->assertSame('r1', $rdvs[0]->getID());
        $this->assertSame('p1', $rdvs[0]->idPraticien);
        $this->assertSame('p2', $rdvs[0]->idPatient);
        $this->assertSame('status', $rdvs[0]->status);
        $this->assertSame('2021-01-01 00:00:00', $rdvs[0]->dateDebut);
    }

    public function testUpdate() {
        $rdv = new Rdv('p1', 'p2', 'status', new DateTimeImmutable('2021-01-01 00:00:00'));
        $id = 'r1';
        $rdv->setID($id);
        $this->rdvRepository->update($rdv);

        $stmt = $this->pdo->prepare('SELECT * FROM rdv WHERE ID = ?');
        $stmt->bindParam(1,$id , PDO::PARAM_STR);
        $stmt->execute();
        $rdvRes = $stmt->fetch();
        $this->assertNotNull($rdvRes);
        $this->assertSame('p1', $rdvRes['idPraticien']);
        $this->assertSame('p2', $rdvRes['idPatient']);
        $this->assertSame('status', $rdvRes['status']);
        $this->assertSame('2021-01-01 00:00:00', $rdvRes['dateDebut']);
    }

    public function getRdvByPraticienIdTest() {
        $rdvs = $this->rdvRepository->getRdvByPraticienId('p1');
        $this->assertCount(2, $rdvs);
        $this->assertSame('r1', $rdvs[0]->getID());
        $this->assertSame('p1', $rdvs[0]->idPraticien);
        $this->assertSame('p2', $rdvs[0]->idPatient);
        $this->assertSame('status', $rdvs[0]->status);
        $this->assertSame('2021-01-01 00:00:00', $rdvs[0]->dateDebut);
    }
}