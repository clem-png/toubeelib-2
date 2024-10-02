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
        try {
            $config = parse_ini_file(__DIR__.'/../config/praticien.db.ini');
            $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['dbname']}";
            $user = $config['username'];
            $password = $config['password'];
            $this->pdo = new PDO($dsn, $user, $password);
            $this->praticienRepository = new PDOPraticienRepository($this->pdo);
        }catch (\Exception $e){
            throw new SkippedTestSuiteError("erreur de setup, skip de tous les tests ---- PraticienRepositoryTest");
        }
    }

    public function testGetSpecialiteById()
    {
        $specialite = $this->praticienRepository->getSpecialiteById('A');
        $this->assertSame('A', $specialite->ID);
        $this->assertSame('Generaliste', $specialite->label);
        $this->assertSame('Medecin generaliste', $specialite->description);
    }

    public function testSave()
    {
        $specialite = new Specialite('A', 'Generaliste', 'Medecin generaliste');
        $praticien = new Praticien('Martin', 'Marie', '123 rue', '1234567890');
        $praticien->setSpecialite($specialite);
        $id = $this->praticienRepository->save($praticien);
        $this->assertIsString($id);

        $stmt = $this->pdo->prepare('SELECT * FROM praticien WHERE ID = ?');
        $stmt->bindParam(1, $id, PDO::PARAM_STR);
        $stmt->execute();
        $praticienRes = $stmt->fetch();
        $this->assertNotNull($praticienRes);
        $this->assertSame('Martin', $praticienRes['nom']);
        $this->assertSame('Marie', $praticienRes['prenom']);
        $this->assertSame('123 rue', $praticienRes['adresse']);
    }

    public function testGetPraticienById()
    {
        $praticien = $this->praticienRepository->getPraticienById('p1');
        $this->assertSame('p1', $praticien->getID());
        $this->assertSame('Martin', $praticien->nom);
        $this->assertSame('Marie', $praticien->prenom);
        $this->assertSame('123 rue', $praticien->adresse);
        $this->assertSame('1234567890', $praticien->tel);
        $this->assertSame('A', $praticien->specialite->ID);
    }


}