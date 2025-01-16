<?php
namespace toubeelib_auth\core\services\auth;

use Psr\Log\LoggerInterface;
use Monolog\Level;
use toubeelib_auth\core\repositoryInterfaces\UserRepositoryInterface;
use toubeelib_auth\core\services\auth\AuthServiceInterface;
use toubeelib_auth\core\services\auth\AuthServiceException;
use toubeelib_auth\core\dto\UserDTO;
use toubeelib_auth\core\dto\InputUserDTO;

class AuthService implements AuthServiceInterface
{
    private UserRepositoryInterface $authRepository;


    public function __construct(UserRepositoryInterface $authRepository){
        $this->authRepository = $authRepository;
    }

    public function verifyCredentials(InputUserDTO $input): UserDTO
    {
        try {
            $user = $this->authRepository->findByEmail($input->email);

            if ($user && password_verify($input->password, $user->password)) {

                return new UserDTO(
                    $user->ID,
                    $user->email,
                    $user->role
                );
            }else{
                throw new AuthServiceException('Identifiants incorrects');
            }
        }catch(\Exception $e){
            throw new AuthServiceException('Erreur de connexion');
        }
    }
}