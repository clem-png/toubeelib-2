<?php

use PHPUnit\Framework\SkippedTestSuiteError;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\infrastructure\repositories\PDORdvRepository;

class RdvRepositoryTest extends TestCase
{
    private $rdvRepository;
    private $pdo;

    protected function setUp(): void
    {
        $config = parse_ini_file(__DIR__.'/../config/iniconf/rdv.db.ini');
        $dsn = "{$config['driver']}:host=localhost;port={$config['port']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->rdvRepository = new PDORdvRepository($this->pdo);
    }
    public function testGetRdvById()
    {
        $rdv = $this->rdvRepository->getRdvById('51d3ce98-4951-4ae0-827b-c436da776d33');
        $this->assertSame('51d3ce98-4951-4ae0-827b-c436da776d33', $rdv->getID());
        $this->assertSame('8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c', $rdv->idPraticien);
        $this->assertSame('d8bfdec3-ca7c-4e72-98f2-749e5f775c86', $rdv->idPatient);
        $this->assertSame('prevu', $rdv->status);
        $this->assertEquals(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2021-01-01 08:00:00'), $rdv->dateDebut);
    }

    public function testSave() {
        $uuid_praticien =  Uuid::uuid4()->toString();
        $uuid_patient =  Uuid::uuid4()->toString();
        $specialite = new Specialite('1d6f853e-f7fe-497f-abdd-7ee1430d14ed', 'Généraliste', 'Médecin généraliste');
        $rdv = new Rdv($uuid_praticien, $uuid_patient, 'prevu', dateDebut: new DateTimeImmutable('2021-01-01 00:00:00'));
        $rdv->setSpecialite($specialite);
        $id = $this->rdvRepository->save($rdv);
        $this->assertIsString($id);

        $stmt = $this->pdo->prepare('SELECT * FROM rdv WHERE ID = ?');
        $stmt->bindParam(1, $id, PDO::PARAM_STR);
        $stmt->execute();
        $rdvRes = $stmt->fetch();
        $this->assertNotNull($rdvRes);
        $this->assertSame($uuid_praticien, $rdvRes['idPraticien']);
        $this->assertSame($uuid_patient, $rdvRes['IdPatient']);
        $this->assertSame('prevu', $rdvRes['status']);
        $this->assertSame('2021-01-01 00:00:00', $rdvRes['dateDebut']);
    }

    public function testGetRdvByPatientId() {
        $rdvs = $this->rdvRepository->getRdvByPatientId('d4ac898d-0d47-48d6-b354-8282bec927ba');
        $this->assertIsArray($rdvs);
        $this->assertCount(1, $rdvs);
        $this->assertSame('89695d04-74eb-4b82-a0bd-b8b802803c57', $rdvs[0]->getID());
        $this->assertSame('cb771755-26f4-4e6c-b327-a1217f5b09cd', $rdvs[0]->idPraticien);
        $this->assertSame('d4ac898d-0d47-48d6-b354-8282bec927ba', $rdvs[0]->idPatient);
        $this->assertSame('prevu', $rdvs[0]->status);
        $this->assertEquals(\DateTimeImmutable::createFromFormat('Y-m-d H:i:s', "2021-01-01 08:00:00"), $rdvs[0]->dateDebut);
    }

    public function testUpdate() {
        $uuid_praticien =  Uuid::uuid4()->toString();
        $uuid_patient =  Uuid::uuid4()->toString();
        $rdv = new Rdv($uuid_praticien, $uuid_patient, 'prevu(changer)', new DateTimeImmutable('2021-01-01 00:00:00'));
        $id = '4ab01147-adca-4326-92e4-7e02bdab12f4';
        $rdv->setID($id);
        $this->rdvRepository->update($rdv);

        $stmt = $this->pdo->prepare('SELECT * FROM rdv WHERE id = ?');
        $stmt->bindParam(1,$id , PDO::PARAM_STR);
        $stmt->execute();
        $rdvRes = $stmt->fetch();
        $this->assertNotNull($rdvRes);
        $this->assertSame($uuid_praticien, $rdvRes['idPraticien']);
        $this->assertSame($uuid_patient, $rdvRes['IdPatient']);
        $this->assertSame('prevu(changer)', $rdvRes['status']);
        $this->assertSame('2021-01-01 00:00:00', $rdvRes['dateDebut']);
    }

    public function testGetRdvByPraticienIdTest() {
        $rdvs = $this->rdvRepository->getRdvByPraticienId('8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c');
        $this->assertCount(1, $rdvs);
        $this->assertSame('51d3ce98-4951-4ae0-827b-c436da776d33', $rdvs[0]->getID());
        $this->assertSame('8a619ff7-7eb3-4d3e-8bb8-43fea421ea7c', $rdvs[0]->idPraticien);
        $this->assertSame('d8bfdec3-ca7c-4e72-98f2-749e5f775c86', $rdvs[0]->idPatient);
        $this->assertSame('prevu', $rdvs[0]->status);
        $this->assertEquals(DateTimeImmutable::createFromFormat("Y-m-d H:i:s", '2021-01-01 08:00:00'), $rdvs[0]->dateDebut);
    }
}