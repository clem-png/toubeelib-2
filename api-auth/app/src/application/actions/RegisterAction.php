<?php

namespace toubeelib_auth\application\actions;

use Exception;
use Slim\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use toubeelib_auth\core\dto\InputUserDTO;
use toubeelib_auth\core\services\user\UserServiceInterface;

class RegisterAction extends AbstractAction
{
    private UserServiceInterface $utilisateurService;

    public function __construct(UserServiceInterface $serviceUtilisateur)
    {
        $this->utilisateurService = $serviceUtilisateur;
    }

  /**
   * @throws Exception
   */
  public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $params = $rq->getParsedBody() ?? null;

        if (!isset($params['email']) || !isset($params['mdp']) || !isset($params['mdp2'])) {
            throw new HttpBadRequestException($rq, 'Paramètres manquants');
        }

        $email = filter_var($params['email'], FILTER_SANITIZE_EMAIL);

        if($params['mdp'] === $params['mdp2']){
          $this->utilisateurService->createUser(new InputUserDTO($email, $params['mdp']));
          return $rs->withStatus(200);
        } else {
          throw new HttpException($rq,'les mots de passe ne sont pas identique');
        }
    }
}