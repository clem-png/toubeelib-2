<?php

namespace toubeelib_auth\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use toubeelib_auth\application\providers\auth\AuthProviderInterface;
use toubeelib_auth\core\dto\InputUserDTO;

class SignInAction extends AbstractAction
{
  private AuthProviderInterface $authProvider;


  /**
   * @param AuthProviderInterface $authProvider
   */
  public function __construct(AuthProviderInterface $authProvider) {
    $this->authProvider = $authProvider;
  }

  /**
   * RECUPERE UN TOKEN A PARTIR DES IDENTIFIANTS PASSER DANS LE BODY DE LA REQUETE ET LE RETOURNE DANS LE CORPS DE LA REPONSE AVEC UN REFRESH TOKEN
   * @param ServerRequestInterface $rq
   * @param ResponseInterface $rs
   * @param array $args
   * @return ResponseInterface
   */
  public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
  {
    $authHeader = $rq->getHeaderLine('Authorization');
    $authHeaderTab = explode(' ', $authHeader);
    if ($authHeaderTab[0] !== 'Basic') {
      throw new HttpUnauthorizedException($rq, 'Authorization header absent ou mal formé');
    }
    $encodedCredentials = $authHeaderTab[1];
    $decodedCredentials = base64_decode($encodedCredentials);
    $credentials = explode(':', $decodedCredentials);

    $email = filter_var($credentials[0], FILTER_SANITIZE_EMAIL);
    $mdp = $credentials[1];

    try {
      $authRes = $this->authProvider->signIn(new InputUserDTO($email, $mdp));
    }catch (Exception $e){
      throw new HttpUnauthorizedException($rq, 'Identifiants incorrects ' . $e->getMessage());
    }

    $response = [
      'type' => 'ressource',
      'atoken' => $authRes->accessToken,
      'rtoken' => $authRes->refreshToken,
      'role' => $authRes->role
    ];

    $rs->getBody()->write(json_encode($response));

    return $rs->withStatus(200)->withHeader('Content-Type', 'application/json');
  }
}