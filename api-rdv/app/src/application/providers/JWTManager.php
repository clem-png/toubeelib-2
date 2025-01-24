<?php
namespace toubeelib_rdv\application\providers;

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

    public function decodeToken(string $token): array{
        return (array) JWT::decode($token,new Key( $this->secretKey, $this->algorithm));
    }

}