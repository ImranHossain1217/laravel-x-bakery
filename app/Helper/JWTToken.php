<?php
namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{
    public static function CreateToken($userEmail)
    {
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time() + 3600,
            'email' => $userEmail
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }
    public static function CreateTokenForResetPassword($userEmail)
    {
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time() + 3600,
            'email' => $userEmail
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }
    public static function VerifyToken($token)
    {
        try {
            $key = env('JWT_KEY');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded->email;
        } catch (Exception $e) {
            return 'unauthorized';
        }
    }
}