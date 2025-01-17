<?php

namespace toubeelib_auth\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use toubeelib_auth\application\actions\AbstractAction;
use toubeelib_auth\application\providers\auth\AuthProviderInterface;
use toubeelib_auth\core\services\auth\UserServiceException;

class RefreshAction extends AbstractAction
{
  private AuthProviderInterface $authProvider;


  /**
   * @param AuthProviderInterface $authProvider
   */
  public function __construct(AuthProviderInterface $authProvider) {
    $this->authProvider = $authProvider;
  }

  /**
   * RECUPERE UN NOUVEAU TOKEN A PARTIR D'UN TOKEN DE REFRESH PASSER DANS LE HEADER
   * @param ServerRequestInterface $rq
   * @param ResponseInterface $rs
   * @param array $args
   * @return ResponseInterface
   */
  public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface{

    try {
      $headers = $rq->getHeader('Authorization');
      if (empty($headers) || !isset($headers[0])) {
        throw new HttpBadRequestException($rq, "Authorization header not found");
      }
      $tokenstring = sscanf($headers[0], "Bearer %s")[0];
    } catch (Exception $e) {
      throw new HttpBadRequestException($rq, "Error retrieving the token: " . $e->getMessage());
    }

    try {
      $authRes = $this->authProvider->refresh($tokenstring);
    }catch (Exception $e){
      throw new HttpUnauthorizedException($rq, 'Identifiants incorrects ' . $e->getMessage());
    }

    $response = [
      'type' => 'ressource',
      'atoken' => $authRes,
    ];

    $rs->getBody()->write(json_encode($response));

    return $rs->withStatus(200)->withHeader('Content-Type', 'application/json');
  }
}