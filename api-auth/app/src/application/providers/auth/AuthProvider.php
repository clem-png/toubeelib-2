<?php
namespace toubeelib\application\providers\auth;

use toubeelib\core\dto\AuthDTO;
use toubeelib\core\dto\InputAuthDTO;
use toubeelib\core\services\auth\AuthServiceException;
use toubeelib\core\services\auth\AuthServiceInterface;
use toubeelib\application\providers\auth\JWTManager;


class AuthProvider implements AuthProviderInterface
{
    
    private AuthServiceInterface $authService;
    private JWTManager $jwtManager;

    public function __construct(AuthServiceInterface $authService, JWTManager $jwtManager)
    {
        $this->authService = $authService;
        $this->jwtManager = $jwtManager;
    }

    public function signIn(InputAuthDTO $credentials): AuthDTO
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
            return new AuthDTO(
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

    public function getSignIn(string $token): AuthDTO{
        $arrayToken = $this->jwtManager->decodeToken($token);

        return new AuthDTO(
            $arrayToken['sub'],
            $arrayToken['data']->email,
            $arrayToken['data']->role
        );

    }
}