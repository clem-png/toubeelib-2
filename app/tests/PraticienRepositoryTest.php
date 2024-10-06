<?php

use PHPUnit\Framework\SkippedTestSuiteError;
use PHPUnit\Framework\TestCase;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\infrastructure\repositories\PDOPraticienRepository;

class PraticienRepositoryTest extends TestCase
{
    private $praticienRepository;
    private $pdo;

    protected function setUp(): void
    {
            $config = parse_ini_file(__DIR__.'/../config/iniconf/praticien.db.ini');
            $dsn = "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            $user = $config['username'];
            $password = $config['password'];
            $this->pdo = new PDO($dsn, $user, $password);
            $this->praticienRepository = new PDOPraticienRepository($this->pdo);
    }

    public function testGetSpecialiteById()
    {
        $specialite = $this->praticienRepository->getSpecialiteById('1d6f853e-f7fe-497f-abdd-7ee1430d14ed');
        $this->assertSame('1d6f853e-f7fe-497f-abdd-7ee1430d14ed', $specialite->ID);
        $this->assertSame('Généraliste', $specialite->label);
        $this->assertSame('Médecin généraliste', $specialite->description);
    }

    public function testSave()
    {
        $idSpe = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $specialite = new Specialite($idSpe, 'test_label', 'test_desc');
        $praticien = new Praticien('Martin_test', 'Marie_test', 'adresse_test', '1234567890');
        $praticien->setSpecialite($specialite);
        $id = $this->praticienRepository->save($praticien);
        $this->assertIsString($id);

        $stmt = $this->pdo->prepare('SELECT * FROM praticien WHERE ID = ?');
        $stmt->bindParam(1, $id, PDO::PARAM_STR);
        $stmt->execute();
        $praticienRes = $stmt->fetch();
        $this->assertNotNull($praticienRes);
        $this->assertSame('Martin_test', $praticienRes['nom']);
        $this->assertSame('Marie_test', $praticienRes['prenom']);
        $this->assertSame('adresse_test', $praticienRes['adresse']);
        $this->assertSame('1234567890', $praticienRes['tel']);
        $stmt = $this->pdo->prepare('SELECT * FROM praticien_spe WHERE "idPraticien" = ? AND "idSpe" = ?');
        $stmt->bindParam(1, $id, PDO::PARAM_STR);
        $stmt->bindParam(2, $idSpe, PDO::PARAM_STR);
        $stmt->execute();
        $praticienSpeRes = $stmt->fetch();
        $this->assertNotNull($praticienSpeRes);
    }

    public function testGetPraticienById()
    {
        $praticien = $this->praticienRepository->getPraticienById('ce5b05aa-714e-486a-ae25-1bc6801403d1');
        $this->assertSame('ce5b05aa-714e-486a-ae25-1bc6801403d1', $praticien->getID());
        $this->assertSame('Martin', $praticien->nom);
        $this->assertSame('Marie', $praticien->prenom);
        $this->assertSame('3lassou', $praticien->adresse);
        $this->assertSame('0123456789', $praticien->tel);
        $this->assertSame('1d6f853e-f7fe-497f-abdd-7ee1430d14ed', $praticien->specialite->ID);
    }

    public function testGetPraticienByTel()
    {
        $praticien = $this->praticienRepository->getPraticienByTel('0123456789');
        $this->assertSame('ce5b05aa-714e-486a-ae25-1bc6801403d1', $praticien->getID());
        $this->assertSame('Martin', $praticien->nom);
        $this->assertSame('Marie', $praticien->prenom);
        $this->assertSame('3lassou', $praticien->adresse);
        $this->assertSame('0123456789', $praticien->tel);
        $this->assertSame('1d6f853e-f7fe-497f-abdd-7ee1430d14ed', $praticien->specialite->ID);
    }
}