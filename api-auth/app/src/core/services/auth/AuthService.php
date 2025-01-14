<?php
namespace toubeelib_auth\core\services\auth;

use Psr\Log\LoggerInterface;
use Monolog\Level;
use toubeelib_auth\core\repositoryInterfaces\AuthRepositoryInterface;
use toubeelib_auth\core\services\auth\AuthServiceInterface;
use toubeelib_auth\core\services\auth\AuthServiceException;
use toubeelib_auth\core\dto\AuthDTO;
use toubeelib_auth\core\dto\InputAuthDTO;

class AuthService implements AuthServiceInterface
{
    private AuthRepositoryInterface $authRepository;

    private LoggerInterface $logger;

    public function __construct(AuthRepositoryInterface $authRepository, LoggerInterface $logger)
    {
        $this->authRepository = $authRepository;
        $this->logger = $logger;
    }

    public function verifyCredentials(InputAuthDTO $input): AuthDTO
    {
        try {
            $user = $this->authRepository->findByEmail($input->email);

            if ($user && password_verify($input->password, $user->password)) {
                $this->logger->log(Level::Info, 'Utilisateur Connecté : ', ['email' => $input->email]);

                return new AuthDTO(
                    $user->ID,
                    $user->email,
                    $user->role
                );
            }else{
                $this->logger->log(Level::Info, 'Connexion échouée : ', ['email' => $input->email]);
                throw new AuthServiceException('Identifiants incorrects');
            }
        }catch(\Exception $e){
            throw new AuthServiceException('Erreur de connexion');
        }
    }
}