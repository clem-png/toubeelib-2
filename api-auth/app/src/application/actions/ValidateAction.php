<?php

namespace toubeelib_auth\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use toubeelib_auth\application\providers\auth\JWTManager;
use toubeelib_auth\core\dto\UserDTO;
use toubeelib_auth\core\dto\InputUserDTO;
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
            $h = $rq->getHeader('Authorization')[0];
            $tokenstring = sscanf($h, "Bearer %s")[0];

            $credentials = $this->jwtManager->decodeToken($tokenstring);
            var_dump($credentials);

        }catch (\Exception $e){
            throw new HttpUnauthorizedException($rq, "erreur auth");
        }

        return $rs->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}