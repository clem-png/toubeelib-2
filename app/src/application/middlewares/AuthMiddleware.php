<?php

namespace toubeelib\application\middlewares;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpUnauthorizedException;
use toubeelib\application\providers\auth\AuthProviderInterface;
use toubeelib\application\providers\auth\JWTManager;
use toubeelib\core\dto\AuthDTO;

class AuthMiddleware{

    protected AuthProviderInterface $provider;

    public function __construct(AuthProviderInterface $p){
        $this->provider = $p;
    }

    public function __invoke(ServerRequestInterface $rq, RequestHandlerInterface $next ): ResponseInterface {


        if (! $rq->hasHeader('Origin'))
            New HttpUnauthorizedException ($rq, "missing Origin Header (auth)");
        if (! $rq->hasHeader("Authorization")){
            New HttpUnauthorizedException ($rq, "missing Authorization Header (auth)");
        }
        if(!isset($rq->getHeader('Authorization')[0])){
            throw new HttpUnauthorizedException($rq,"no auth, try /users/signin");
        }
        if(strlen($rq->getHeader('Authorization')[0]) == 6){
            throw new HttpUnauthorizedException($rq,"no auth, try /users/signin");
        }

        try{
            $h = $rq->getHeader('Authorization')[0];
            $tokenstring = sscanf($h, "Bearer %s")[0];
            $authDTO = $this->provider->getSignIn($tokenstring);


        }catch (ExpiredException $e) {
            throw new HttpUnauthorizedException($rq,"expired token");
        } catch (SignatureInvalidException $e) {
            throw new HttpUnauthorizedException($rq,"signature invalid token");
        } catch (BeforeValidException $e) {
            throw new HttpUnauthorizedException($rq,"before valid token");
        } catch (\UnexpectedValueException $e) {
            throw new HttpUnauthorizedException($rq,"unexpected value token");
        }

        $rq = $rq->withAttribute('AuthDTO',$authDTO);

        $response = $next->handle($rq);
        return $response;
    }

}