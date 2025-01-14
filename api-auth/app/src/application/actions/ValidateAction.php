<?php

namespace toubeelib_auth\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use toubeelib_auth\application\providers\auth\JWTManager;
use toubeelib_auth\core\dto\AuthDTO;
use toubeelib_auth\core\dto\InputAuthDTO;
use toubeelib_auth\core\services\auth\AuthServiceInterface;

class ValidateAction extends AbstractAction
{

    private AuthServiceInterface $authService;
    private JWTManager $jwtManager;

    public function __construct(AuthServiceInterface $authService, JWTManager $jwtManager)
    {
        $this->authService = $authService;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        try {
            $token_line = $rq->hasHeader('Authorization');
            list($token) = sscanf($token_line, "Bearer %s");
            $credentials = $this->jwtManager->decodeToken($token);
            $inputAuthDTO = new InputAuthDTO($credentials['email'], $credentials['mdp']);
            $authDTO = $this->authService->verifyCredentials($inputAuthDTO);
        }catch (\Exception $e){
            throw new HttpUnauthorizedException($rq, "erreur auth");
        }

        return $rs->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}