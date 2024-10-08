<?php

namespace toubeelib\application\actions;

use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;

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
        $authRes = $this->authProvider->verifyCrendentials($user, $password);
        if ($authRes === false) {
            throw new HttpUnauthorizedException($rq, 'Identifiants incorrects');
        }


        /* truc pour le token
        $payload = [
            'iat'=>time(),
            'exp'=>time()+3600,
            'sub' => $authRes['id'],
            'data' => [
                'role' => $authRes['role'],
                'user' => $authRes['user']
            ]
        ] ;

        $secret = ContainerInterface::class->get('SECRET_KEY');
        $token = JWT::encode($payload, $secret, 'HS512');
        */
        $response = [
            'type' => 'ressource',
            'atoken' => $authRes['atoken'],
            'rtoken' => $authRes['rtoken']
        ];

        $rs->getBody()->write(json_encode($response));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}