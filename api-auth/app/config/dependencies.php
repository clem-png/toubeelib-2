<?php

use Psr\Container\ContainerInterface;
use toubeelib_auth\application\actions\RefreshAction;
use toubeelib_auth\application\actions\RegisterAction;
use toubeelib_auth\application\actions\SignInAction;
use toubeelib_auth\application\actions\ValidateAction;
use toubeelib_auth\application\providers\auth\AuthProvider;
use toubeelib_auth\application\providers\auth\AuthProviderInterface;
use toubeelib_auth\application\providers\auth\JWTManager;
use toubeelib_auth\core\repositoryInterfaces\UserRepositoryInterface;
use toubeelib_auth\core\services\auth\AuthService;
use toubeelib_auth\core\services\auth\AuthServiceInterface;
use toubeelib_auth\core\services\user\UserService;
use toubeelib_auth\core\services\user\UserServiceInterface;
use toubeelib_auth\infrastructure\repositories\PDOUserRepository;


return [

    JWTManager::class => function(ContainerInterface $c){
        return new JWTManager($c->get('SECRET_KEY'));
    },

    AuthProviderInterface::class => function(ContainerInterface $c){
        return new AuthProvider($c->get(AuthServiceInterface::class),$c->get(JWTManager::class));
    },

    UserRepositoryInterface::class => function(ContainerInterface $c){
        return new PDOUserRepository($c->get('user.pdo'));
    },

    UserServiceInterface::class => function(ContainerInterface $c){
        return new UserService($c->get(UserRepositoryInterface::class));
    },

    AuthServiceInterface::class => function(ContainerInterface $c){
        return new AuthService($c->get(UserRepositoryInterface::class));
    },

    SignInAction::class => function(ContainerInterface $c){
        return new SignInAction($c->get(AuthProviderInterface::class));
    },

    RegisterAction::class => function(ContainerInterface $c){
        return new RegisterAction($c->get(UserServiceInterface::class));
    },

    RefreshAction::class => function(ContainerInterface $c){
        return new RefreshAction($c->get(AuthProviderInterface::class));
    },

    ValidateAction::class => function(ContainerInterface $c){
        return new ValidateAction($c->get(AuthProviderInterface::class));
    }

];