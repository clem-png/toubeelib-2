<?php
namespace toubeelib_auth\application\providers\auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTManager
{
    private $secretKey;
    private $algorithm;

    public function __construct($secretKey, $algorithm = 'HS256')
    {
        $this->secretKey = $secretKey;
        $this->algorithm = $algorithm;
    }

    public function createAccessToken(array $payload): string{
        $payload['exp'] = time() + 3600;
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function createRefreshToken(array $payload): string{
        $payload['exp'] = time() + 3600 * 24 * 7;
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function decodeToken(string $token): array{
        return (array) JWT::decode($token,new Key( $this->secretKey, $this->algorithm));
    }

}