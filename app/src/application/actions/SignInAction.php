<?php

namespace toubeelib\application\actions;

use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use toubeelib\application\providers\auth\AuthProviderInterface;
use toubeelib\core\dto\InputAuthDTO;
use toubeelib\core\services\auth\AuthServiceException;

class SignInAction extends AbstractAction
{
    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $authHeader = $rq->getHeaderLine('Authorization');
        $authHeaderTab = explode(' ', $authHeader);
        if ($authHeaderTab[0] !== 'Basic') {
            throw new HttpUnauthorizedException($rq, 'Authorization header absent ou mal formé');
        }
        $encodedCredentials = $authHeader[1];
        $decodedCredentials = base64_decode($encodedCredentials);
        $credentials = explode(':', $decodedCredentials);

        $user = $credentials[0];
        $password = $credentials[1];
        try {
            $authRes = $this->authProvider->signIn(new InputAuthDTO($user, $password));
        }catch (AuthServiceException $e){
            throw new HttpUnauthorizedException($rq, 'Identifiants incorrects');
        }

        $response = [
            'type' => 'ressource',
            'atoken' => $authRes['accessToken'],
            'rtoken' => $authRes['refreshToken']
        ];

        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}