<?php

namespace toubeelib_rdv\application\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

use Slim\Exception\HttpUnauthorizedException;

use toubeelib_rdv\application\providers\JWTManager;
use toubeelib_rdv\core\services\authorisation\AuthorisationServiceInterface;

class AuthorisationMiddleware{

    private JWTManager $jwtManager;
    private AuthorisationServiceInterface $authorisationService;

    public function __construct(AuthorisationServiceInterface $authorisation ,JWTManager $jwtManager){
        $this->jwtManager = $jwtManager;
        $this->authorisationService = $authorisation;
    }

    public function __invoke(ServerRequestInterface $rq, RequestHandlerInterface $next): ResponseInterface{
        // Récupération de l'id de la ressource demandée
        $routeContext = RouteContext::fromRequest($rq);
        $route = $routeContext->getRoute();
        $routeName = $route->getName();
    
        // Récupération du token et de l'id de l'utilisateur connecté
        $h = $rq->getHeader('Authorization')[0];
        $tokenstring = sscanf($h, "Bearer %s")[0];
        $tokenData = $this->jwtManager->decodeToken($tokenstring);
        $userId = $tokenData['sub'];
    
        // Vérification des droits d'accès selon la route
        switch ($routeName) {
            case 'rdvsId':
                $idRdv = $route->getArgument('id');
                if (!$this->authorisationService->authoriseAccesRDV($idRdv, $userId)) {
                    throw new HttpUnauthorizedException($rq, 'Vous n\'avez pas les droits pour accéder à cette ressource');
                }
                break;
            case 'praticiensPlanning':
                $idPraticien = $route->getArgument('id');
                if (!$this->authorisationService->authorisePlanningAccess($userId, $idPraticien)) {
                    throw new HttpUnauthorizedException($rq, 'Vous n\'avez pas les droits pour accéder à cette ressource');
                }
                break;
            case 'rdvsAdd':
                $body = $rq->getParsedBody();
                $idPatient = $body['idPatient'] ?? null;
                $idPraticien = $body['idPraticien'] ?? null;
                if (!$this->authorisationService->authoriseCreateRDV($userId, $idPatient, $idPraticien)) {
                    throw new HttpUnauthorizedException($rq, 'Vous n\'avez pas les droits pour créer cette ressource');
                }
                break;
            default:
                throw new HttpUnauthorizedException($rq, 'Route non autorisée');
        }
    
        $response = $next->handle($rq);
        return $response;
    }
}