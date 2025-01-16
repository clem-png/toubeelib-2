<?php

namespace toubeelib_auth\core\services\user;

use toubeelib_auth\core\dto\UserDTO;
use toubeelib_auth\core\domain\entities\user\User;
use toubeelib_auth\core\dto\InputUserDTO;
use toubeelib_auth\core\repositoryInterfaces\UserRepositoryInterface;
use toubeelib_auth\core\services\auth\UserServiceException;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepository;


    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(InputUserDTO $input): void{
        try {
            $user = $this->userRepository->findByEmail($input->email);
            if ($user) {
                throw new UserServiceException('Email déjà utilisé');
            }
            $user = new User(
                $input->email,
                password_hash($input->password, PASSWORD_DEFAULT),
                0
            );
            $this->userRepository->save($user);
        } catch (\Exception $e) {
            throw new UserServiceException('Erreur lors de la création de l\'utilisateur');
        }
        
    }

    public function findUserById(string $ID): UserDTO
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