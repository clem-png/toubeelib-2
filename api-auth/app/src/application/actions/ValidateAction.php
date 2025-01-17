<?php

namespace toubeelib_auth\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use toubeelib_auth\core\dto\UserDTO;
use toubeelib_auth\core\dto\InputUserDTO;
use toubeelib_auth\application\providers\auth\AuthProviderInterface;

class ValidateAction extends AbstractAction
{

    private AuthProviderInterface $provider;

    public function __construct(AuthProviderInterface $provider)
    {   
        $this->provider = $provider;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        try {
            $h = $rq->getHeader('Authorization')[0];
            $tokenstring = sscanf($h, "Bearer %s")[0];
            $utiOutDTO = $this->provider->getSignIn($tokenstring);
        }catch (ExpiredException $e) {
            throw new HttpUnauthorizedException($rq,"expired token");
        } catch (SignatureInvalidException $e) {
            throw new HttpUnauthorizedException($rq,"signature invalid token");
        } catch (BeforeValidException $e) {
            throw new HttpUnauthorizedException($rq,"before valid token");
        } catch (\UnexpectedValueException $e) {
            throw new HttpUnauthorizedException($rq,"unexpected value token");
        }

        $rq = $rq->withAttribute('UtiOutDTO',$utiOutDTO);

        return $rs->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}