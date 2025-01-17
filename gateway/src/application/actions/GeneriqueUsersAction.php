<?php

namespace gateway\application\actions;

use gateway\application\actions\AbstractAction;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;

class GeneriqueUsersAction extends AbstractAction{

    private ClientInterface $remote_api;

    public function __construct(ClientInterface $api_client)
    {
        $this->remote_api = $api_client;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $method = $rq->getMethod();
        $path = $rq->getUri()->getPath();
        $options = ['query' => $rq->getQueryParams()];

        if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH') {
            $options['json'] = $rq->getParsedBody();
        }

        $auth = $rq->getHeader('Authorization') ?? null;
        if (!empty($auth)) {
            $options['headers'] = ['Authorization' => $auth];
        }

        try {

            $path2 = explode('/',$path)[2];

            $rs = $this->remote_api->request($method, '/'.$path2,$options);
        } catch (ConnectException | ServerException $e) {
            throw new HttpInternalServerErrorException($rq, "The remote server is not available");
        }catch (ClientException $e) {
            match($e->getCode()) {
                400 => throw new HttpBadRequestException($rq, "The request is invalid"),
                401 => throw new HttpUnauthorizedException($rq, "You are not authorized to access this resource"),
                403 => throw new HttpForbiddenException($rq, "You are not allowed to access this resource"),
                404 => throw new HttpNotFoundException($rq, "The requested resource was not found"),
            };
        }
        return $rs;
    }
}