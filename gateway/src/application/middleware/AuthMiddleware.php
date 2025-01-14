<?php

namespace gateway\application\middleware;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
//use nrv\application\providers\auth\AuthProviderInterface;
//use nrv\application\providers\auth\JWTManager;

class AuthMiddleware{
    protected AuthProviderInterface $provider;

    /**
     * @param AuthProviderInterface $p
     */
    public function __construct(AuthProviderInterface $p){
        $this->provider = $p;
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

        try{

            $token_line = $rq->hasHeader('Authorization');
            list($token) = sscanf($token_line, "Bearer %s");

        }catch (ExpiredException $e) {
            throw new HttpUnauthorizedException($rq,"expired token");
        } catch (SignatureInvalidException $e) {
            throw new HttpUnauthorizedException($rq,"signature invalid token");
        } catch (BeforeValidException $e) {
            throw new HttpUnauthorizedException($rq,"before valid token");
        } catch (\UnexpectedValueException $e) {
            throw new HttpUnauthorizedException($rq,"unexpected value token");
        }

        try {
            $response = $this->auth_service->request('POST', '/tokens/validate', [
                'json' => ['token' => $token]
            ]);
        } catch (ConnectException | ServerException $e) {
        } catch (ClientException $e ) {
            match($e->getCode()) {
                401 => throw new HttpUnauthorizedException($rq, "unauthorized ({$e->getCode()}, {$e->getMessage()})"),
                403 => throw new HttpUnauthorizedException($rq, "forbidden ({$e->getCode()}, {$e->getMessage()})"),
            };
        }

        $response = $next->handle($rq);
        return $response;
    }


}