<?php
namespace toubeelib_auth\application\providers\auth;

use toubeelib_auth\core\dto\InputUserDTO;
use toubeelib_auth\core\dto\UserDTO;
use toubeelib_auth\core\services\auth\AuthServiceException;
use toubeelib_auth\core\services\auth\AuthServiceInterface;
use toubeelib_auth\application\providers\auth\JWTManager;

class AuthProvider implements AuthProviderInterface
{
    
    private AuthServiceInterface $authService;
    private JWTManager $jwtManager;

    public function __construct(AuthServiceInterface $authService, JWTManager $jwtManager)
    {
        $this->authService = $authService;
        $this->jwtManager = $jwtManager;
    }

    public function signIn(InputUserDTO $credentials): UserDTO
    {
        try{
            // Verifier les credentials
            $authDTO = $this->authService->verifyCredentials($credentials);

            // Creer le payload pour les tokens
            $payload = [
                'iat'=>time(),
                'exp'=>time()+3600,
                'sub' => $authDTO->id,
                'data' => [
                    'role' => $authDTO->role,
                    'email' => $authDTO->email,
                ]
            ];

            // Creer les tokens
            $accessToken = $this->jwtManager->createAccessToken($payload);
            $refreshToken = $this->jwtManager->createRefreshToken($payload);

            // Retourner le AuthDTO avec les tokens
            return new UserDTO(
                $authDTO->id,
                $authDTO->email,
                $authDTO->role,
                $accessToken,
                $refreshToken
            );
        }catch(\Exception $e){
            throw new AuthServiceException("erreur auth");
        }
    }

    public function getSignIn(string $token): UserDTO{
        $arrayToken = $this->jwtManager->decodeToken($token);

        return new UserDTO(
            $arrayToken['sub'],
            $arrayToken['data']->email,
            $arrayToken['data']->role
        );
    }

    public function refresh(string $token): string {
        $arrayToken = $this->jwtManager->decodeToken($token);
        $payload = [
            'iat'=>time(),
            'exp'=>time()+3600,
            'sub' => $arrayToken['sub'],
            'data' => [
                'role' => $arrayToken['data']->role,
                'email' => $arrayToken['data']->email,
            ]
        ];

        $accessToken = $this->jwtManager->createAccessToken($payload);

        return $accessToken;
    }
}