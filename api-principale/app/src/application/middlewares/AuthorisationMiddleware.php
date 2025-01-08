<?php

namespace toubeelib\application\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use Slim\Exception\HttpForbiddenException;
use Slim\Routing\RouteContext;
use toubeelib\core\services\authorization\AuthzPraticienServiceInterface;

class AuthorisationMiddleware{

    protected AuthzPraticienServiceInterface $authServInter;

    public function __construct(AuthzPraticienServiceInterface $ast){
        $this->authServInter = $ast;
    }

    public function __invoke(ServerRequestInterface $rq, RequestHandlerInterface $next): ResponseInterface{

        $routeContext = RouteContext::fromRequest($rq);
        $route = $routeContext->getRoute();
        $ressourceId = $route->getArgument('ID-PRATICIEN');
        //$ressourceId = $rq->getAttribute('ID-PRATICIEN');

        $methode = $rq->getMethod();
        $operation = -1;

        if($ressourceId == null){
            $ressourceId = "";
        }

        switch ($methode){
            case 'POST':
                $operation = 1;
                break;
            case 'GET':
                $operation = 0;
                break;
            case 'DELETE':
                $operation = 2;
                break;
            default:
                $operation = -1;
                break;
        }

        $authDTO = $rq->getAttribute('AuthDTO');

        $role = $authDTO->role;
        $userid = $authDTO->id;

        if(!$this->authServInter->isGranted($userid,$role,$operation,$ressourceId)){
            throw new HttpForbiddenException($rq,"not authorized");
        }

        $response = $next->handle($rq);
        return $response;
    }

}