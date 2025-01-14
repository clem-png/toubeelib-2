<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use toubeelib_auth\application\actions\HomeAction;
use toubeelib_auth\application\actions\RefreshAction;
use toubeelib_auth\application\actions\RegisterAction;
use toubeelib_auth\application\actions\SignInAction;
use toubeelib_auth\application\actions\ValidateAction;
use toubeelib_praticiens\application\actions\GetPraticiensAction;


return function( App $app): App {

    $app->options('/{routes:.+}',
        function( Request $rq,
                  Response $rs, array $args) : Response {
            return $rs;
        });

    $app->get('/', HomeAction::class);

    $app->post('/token/signin[/]',SignInAction::class)
        ->setName('tokenSignin');

    $app->post('/token/register[/]',RegisterAction::class)
        ->setName('tokenRegister');

    $app->post('/token/refresh[/]',RefreshAction::class)
        ->setName('tokenRefresh');

    $app->post('/token/validate[/]',ValidateAction::class)
        ->setName('tokenValidate');

    return $app;
};