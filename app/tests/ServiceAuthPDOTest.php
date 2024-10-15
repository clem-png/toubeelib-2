<?php

use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\SkippedTestSuiteError;
use PHPUnit\Framework\TestCase;
use toubeelib\infrastructure\repositories\PDOAuthRepository;
use toubeelib\core\services\auth\AuthService;
use toubeelib\core\dto\InputAuthDTO;
use toubeelib\core\services\auth\AuthServiceException;

class ServiceAuthPDOTest extends TestCase
{
    private $authRepository;
    private $authService;
    private $pdo;

    protected function setUp(): void
    {   
        $logger = new \Monolog\Logger('test.log');
        $logger->pushHandler(new StreamHandler(__DIR__.'/test.log',\Monolog\Level::Info));

        $config = parse_ini_file(__DIR__.'/../config/iniconf/users.db.ini');
        $dsn = "{$config['driver']}:host=localhost;port={$config['port']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->authRepository = new PDOAuthRepository($this->pdo);            
        $this->authService = new AuthService($this->authRepository, $logger);
    }

    public function testVerifyCredentials()
    {
        $inputDTO = new InputAuthDTO('test@test.fr', 'test');
        $authDTO = $this->authService->verifyCredentials($inputDTO);
        $this->assertNotNull($authDTO);
        $this->assertSame('cb771755-26f4-4e6c-b327-a1217f5b09cd', $authDTO->id);
        $this->assertSame('test@test.fr', $authDTO->email);
        $this->assertSame(0, $authDTO->role);

        // Test avec un mauvais mot de passe
        $inputDTO = new InputAuthDTO('test@test.fr', 'mauvais');
        $this->expectException(toubeelib\core\services\auth\AuthServiceException::class);
        $this->authService->verifyCredentials($inputDTO);

        // Test avec un mauvais email
        $inputDTO = new InputAuthDTO('mauvais@email.fr', 'test');
        $this->expectException(toubeelib\core\services\auth\AuthServiceException::class);
        $this->authService->verifyCredentials($inputDTO);

        // Test avec un mauvais email et un mauvais mot de passe
        $inputDTO = new InputAuthDTO('mauvais@email.fr', 'mauvais');
        $this->expectException(toubeelib\core\services\auth\AuthServiceException::class);
        $this->authService->verifyCredentials($inputDTO);

        // Test avec un email vide
        $inputDTO = new InputAuthDTO('', 'test');
        $this->expectException(toubeelib\core\services\auth\AuthServiceException::class);
        $this->authService->verifyCredentials($inputDTO);

        // Test avec un mot de passe vide
        $inputDTO = new InputAuthDTO('test@test.fr', '');
        $this->expectException(toubeelib\core\services\auth\AuthServiceException::class);
        $this->authService->verifyCredentials($inputDTO);

        // test sans InputAuthDTO
        $this->expectException(TypeError::class);
        $this->authService->verifyCredentials();
        
    }
}