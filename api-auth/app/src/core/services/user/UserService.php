<?php

namespace toubeelib_auth\core\services\user;

use toubeelib_auth\core\dto\AuthDTO;
use toubeelib_auth\core\domain\entities\auth\Auth;
use toubeelib_auth\core\dto\InputAuthDTO;
use toubeelib_auth\core\repositoryInterfaces\AuthRepositoryInterface;
use toubeelib_auth\core\services\auth\UserServiceException;

class UserService implements UserServiceInterface
{
    private AuthRepositoryInterface $userRepository;


    public function __construct(AuthRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(InputAuthDTO $input): void{
        try {
            $user = $this->userRepository->findByEmail($input->email);
            if ($user) {
                throw new UserServiceException('Email déjà utilisé');
            }
            $user = new Auth(
                $input->email,
                password_hash($input->password, PASSWORD_DEFAULT),
                0
            );
            $this->userRepository->save($user);
        } catch (\Exception $e) {
            throw new UserServiceException('Erreur lors de la création de l\'utilisateur');
        }
        
    }

    public function findUserById(string $ID): AuthDTO
    {
        try {
            $user = $this->userRepository->findById($ID);
            if (!$user) {
                throw new UserServiceException('Utilisateur introuvable');
            }
            return $user->toDTO();
        } catch (\Exception $e) {
            throw new UserServiceException('Erreur lors de la recherche de l\'utilisateur');
        }
    }
}