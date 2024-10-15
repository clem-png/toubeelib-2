<?php

use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;
use toubeelib\core\dto\InputPraticienDTO;
use toubeelib\core\dto\InputSearchDTO;
use toubeelib\core\dto\PraticienDTO;
use toubeelib\core\dto\InputSpecialiteDTO;
use toubeelib\core\services\praticien\ServicePraticienInvalidDataException;
use toubeelib\core\services\praticien\ServicePraticien;
use toubeelib\infrastructure\repositories\PDOPraticienRepository;

class ServicePraticienPDOTest extends TestCase
{
    private $praticienRepository;
    private $praticienService;
    private $pdo;

    protected function setUp(): void
    {
        $logger = new \Monolog\Logger('test.log');
        $logger->pushHandler(new StreamHandler(__DIR__.'/test.log', \Monolog\Level::Info));
        $config = parse_ini_file(__DIR__.'/../config/iniconf/praticien.db.ini');
        $dsn = "{$config['driver']}:host=localhost;port={$config['port']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        $this->pdo = new PDO($dsn, $user, $password);
        $this->praticienRepository = new PDOPraticienRepository($this->pdo);
        $this->praticienService = new ServicePraticien($this->praticienRepository, $logger);
    }

    public function testGetPraticienParId()
    {
        $praticien_id = 'cb771755-26f4-4e6c-b327-a1217f5b09cd';
        $result = $this->praticienService->getPraticienById($praticien_id);

        $this->assertSame('cb771755-26f4-4e6c-b327-a1217f5b09cd', $result->ID);

        $praticien_id = 'testid';
        $this->expectException(ServicePraticienInvalidDataException::class);
        $this->praticienService->getPraticienById($praticien_id);  
    }

    public function testCreerPraticien()
    {
        // Test creation d'un nouveau praticien
        $specialite = new InputSpecialiteDTO('dd3cac4c-c175-427c-b2aa-8fcc54f250b5');
        $inputPraticienDTO = new InputPraticienDTO('Martin_test', 'Marie_test', 'adresse_test', 'tel_test', $specialite);
        $result = $this->praticienService->createPraticien($inputPraticienDTO);

        $this->assertInstanceOf(PraticienDTO::class, $result);
        $this->assertSame('Martin_test', $result->nom);
        $this->assertSame('Marie_test', $result->prenom);
        $this->assertSame('adresse_test', $result->adresse);
        $this->assertSame('tel_test', $result->tel);

        // Test exception si creation d'un praticien existants
        $this->expectException(ServicePraticienInvalidDataException::class);
        $this->praticienService->createPraticien($inputPraticienDTO);

        // Test exception si specialite non trouvee
        $specialite = new InputSpecialiteDTO('testId');
        $inputPraticienDTO = new InputPraticienDTO('Jean', 'Martin', '456 Avenue', '0987654321', $specialite);

        $this->expectException(ServicePraticienInvalidDataException::class);
        $this->praticienService->createPraticien($inputPraticienDTO);

        // Test exception si creation d'un praticien sans specialite
        $inputPraticienDTO = new InputPraticienDTO('Jean', 'Martin', '456 Avenue', '0987654321', null);

        $this->expectException(ServicePraticienInvalidDataException::class);
        $this->praticienService->createPraticien($inputPraticienDTO);
    }

    public function testGetPraticienParTel(){
        $tel = '0123456789';
        $result = $this->praticienService->getPraticienByTel($tel);

        // Test si le praticien est bien trouve
        $this->assertSame('cb771755-26f4-4e6c-b327-a1217f5b09cd', $result->ID);

        // Test si le praticien n'est pas trouve
        $tel = '9999999999';
        $this->expectException(ServicePraticienInvalidDataException::class);
        $this->praticienService->getPraticienByTel($tel);
    }

    public function testGetSpecialiteParId(){
        $id = 'dd3cac4c-c175-427c-b2aa-8fcc54f250b5';
        $result = $this->praticienService->getSpecialiteById($id);

        // Test si la specialite est bien trouvee
        $this->assertSame('dd3cac4c-c175-427c-b2aa-8fcc54f250b5', $result->ID);
        $this->assertSame('Dentiste', $result->label);

        // Test si la specialite n'est pas trouvee
        $id = 'testId';
        $this->expectException(ServicePraticienInvalidDataException::class);
        $this->praticienService->getSpecialiteById($id);
    }

    public function testSearchPraticien(){
        $inputSearchDTO = new InputSearchDTO('Dupont', 'Jean', null, null);
        $results = $this->praticienService->searchPraticiens($inputSearchDTO);

        $this->assertCount(1, $results);
        $this->assertSame('Dupont', $results[0]->nom);
        $this->assertSame('Jean', $results[0]->prenom);
        $this->assertSame('nancy', $results[0]->adresse);
        $this->assertSame('0123456789', $results[0]->tel);

        // Test seach avec telephone
        $inputSearchDTO = new InputSearchDTO(null, null, null, '0123456789');
        $results = $this->praticienService->searchPraticiens($inputSearchDTO);

        $this->assertCount(1, $results);
        $this->assertSame('Dupont', $results[0]->nom);
        $this->assertSame('Jean', $results[0]->prenom);
        $this->assertSame('nancy', $results[0]->adresse);
        $this->assertSame('0123456789', $results[0]->tel);

        // Test search avec nom partiel
        $inputSearchDTO = new InputSearchDTO('Du', null, null, null);
        $results = $this->praticienService->searchPraticiens($inputSearchDTO);

        $this->assertCount(2, $results);
        $this->assertSame('Dupont', $results[0]->nom);
        $this->assertSame('Jean', $results[0]->prenom);
        $this->assertSame('Durand', $results[1]->nom);
        $this->assertSame('Pierre', $results[1]->prenom);


        // Test si aucun praticien n'est trouve
        $inputSearchDTO = new InputSearchDTO('NonExistent', 'Praticien', 'Unknown Address', '0000000000');
        $results = $this->praticienService->searchPraticiens($inputSearchDTO);
        $this->assertCount(0, $results);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DELETE FROM praticien WHERE nom = \'Martin_test\' AND prenom = \'Marie_test\' AND adresse = \'adresse_test\' AND tel = \'tel_test\'');
        $this->pdo->exec('DELETE FROM praticien_spe WHERE "idPraticien" = (SELECT ID FROM praticien WHERE nom = \'Martin_test\' AND prenom = \'Marie_test\' AND adresse = \'adresse_test\' AND tel = \'tel_test\')');
    }
}