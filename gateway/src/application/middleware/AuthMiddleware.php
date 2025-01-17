<?php

namespace gateway\application\middleware;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
//use nrv\application\providers\auth\AuthProviderInterface;
//use nrv\application\providers\auth\JWTManager;

class AuthMiddleware{

    private ClientInterface $client_auth;


    public function __construct(ClientInterface $c){
        $this->client_auth = $c;
    }

    /**
     * VERIFIE SI L'UTILISATEUR EST CONNECTE EN VERIFIANT LE TOKEN
     * @param ServerRequestInterface $rq
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $rq, RequestHandlerInterface $next ): ResponseInterface {


        if (! $rq->hasHeader('Origin'))
            New HttpUnauthorizedException ($rq, "missing Origin Header (auth)");
        if (! $rq->hasHeader("Authorization")){
            New HttpUnauthorizedException ($rq, "missing Authorization Header (auth)");
        }
        if(!isset($rq->getHeader('Authorization')[0])){
            throw new HttpUnauthorizedException($rq,"no auth, try /users/signin[/] or /users/signup[/]");
        }
        if(strlen($rq->getHeader('Authorization')[0]) == 6){
            throw new HttpUnauthorizedException($rq,"no auth, try /users/signin[/] or /users/signup[/]");
        }

        $h = $rq->getHeader('Authorization');
        if($h == null || empty($h)){
            throw new HttpUnauthorizedException($rq,"no auth, try /users/signin[/] or /users/signup[/]");
        }
        try {
            $options = ['query' => $rq->getQueryParams()];
            $options['headers'] = ['Authorization' => $h];
            $response = $this->client_auth->request('POST', '/token/validate',$options);
        } catch (ConnectException | ServerException $e) {
        } catch (ClientException $e ) {
            match($e->getCode()) {
                400 => throw new HttpBadRequestException($rq, "bad request ({$e->getCode()}, {$e->getMessage()})"),
                401 => throw new HttpUnauthorizedException($rq, "unauthorized ({$e->getCode()}, {$e->getMessage()})"),
                403 => throw new HttpForbiddenException($rq, "forbidden ({$e->getCode()}, {$e->getMessage()})"),
                404 => throw new HttpNotFoundException($rq, "forbidden ({$e->getCode()}, {$e->getMessage()})"),
            };
        }

        $response = $next->handle($rq);
        return $response;
    }


}