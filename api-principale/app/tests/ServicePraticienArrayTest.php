<?php

use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;
use toubeelib\core\dto\InputPraticienDTO;
use toubeelib\core\dto\PraticienDTO;
use toubeelib\core\dto\InputSpecialiteDTO;
use toubeelib\core\services\praticien\ServicePraticienInvalidDataException;
use toubeelib\infrastructure\repositories\ArrayPraticienRepository;
use toubeelib\core\services\praticien\ServicePraticien;

class ServicePraticienArrayTest extends TestCase
{
    private $praticienRepository;
    private $praticienService;

    protected function setUp(): void
    {
        $logger = new \Monolog\Logger('test.log');
        $logger->pushHandler(new StreamHandler(__DIR__.'/test.log',\Monolog\Level::Info));
        $this->praticienRepository = new ArrayPraticienRepository();
        $this->praticienService = new ServicePraticien($this->praticienRepository, $logger);
    }

    public function testGetPraticienParId()
    {
        $praticien_id = 'p1';
        $result = $this->praticienService->getPraticienById($praticien_id);

        $this->assertSame('p1', $result->ID);

        $praticien_id = 'testid';
        $this->expectException(ServicePraticienInvalidDataException::class);
        $this->praticienService->getPraticienById($praticien_id);  
    }

    public function testCreerPraticien()
    {
        // Test creation d'un nouveau praticien
        $specialite = new InputSpecialiteDTO('A');
        $inputPraticienDTO = new InputPraticienDTO('Martin', 'Marie', '123 rue', '1234567890', $specialite);
        $result = $this->praticienService->createPraticien($inputPraticienDTO);

        $this->assertInstanceOf(PraticienDTO::class, $result);
        $this->assertSame('Martin', $result->nom);
        $this->assertSame('Marie', $result->prenom);
        $this->assertSame('123 rue', $result->adresse);
        $this->assertSame('1234567890', $result->tel);

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
        $tel = '9876543210';
        $result = $this->praticienService->getPraticienByTel($tel);

        // Test si le praticien est bien trouve
        $this->assertSame('p2', $result->ID);

        // Test si le praticien n'est pas trouve
        $tel = '9999999999';
        $this->expectException(ServicePraticienInvalidDataException::class);
        $this->praticienService->getPraticienByTel($tel);
    }

    public function testGetSpecialiteParId(){
        $id = 'A';
        $result = $this->praticienService->getSpecialiteById($id);

        // Test si la specialite est bien trouvee
        $this->assertSame('A', $result->ID);
        $this->assertSame('Dentiste', $result->label);

        // Test si la specialite n'est pas trouvee
        $id = 'testId';
        $this->expectException(ServicePraticienInvalidDataException::class);
        $this->praticienService->getSpecialiteById($id);
    }
}