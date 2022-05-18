<?php
namespace App\Services;

use Firebase\JWT\JWT;
use App\Entity\User;

class JwtAuth{
    public $manager;
    public $key;

    public function __construct($manager){
        $this->manager = $manager;
        $this->key = 'esto_es_una_clave_de_ejemplo123153103';
    }

    public function signup($email, $password, $getToken = null){
        $user = $this->manager->getRepository(User::class)->findOneBy([
            'email' => $email,
            'password' => $password
        ]);

        $signup = false;
        if (is_object($user)){
            $signup = true;
        }

        if($signup){
            $token = [
                'sub' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'email' => $user->getEmail(),
                'iat' => time(),
                'exp' => time() + (7*24*60*60)
            ];

            $jwt = JWT::encode($token, $this->key, 'HS256');
            if (!empty($getToken)){
                $data = $jwt;
            } else {
                $decoded = JWT::decode($jwt, $this->key, ['HS256']);
                $data = $decoded;
            }
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Login incorrecto'
            ];
        }
        return $data;
    }

    public function checkToken($jwt, $identity = false){
        $auth = false;

        try {
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }catch (\UnexpectedValueException $e){
            $auth = false;
        } catch (\DomainException $e){
            $auth = false;
        }


        if(isset($decoded) && !empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        } else {
            $auth = false;
        }

        if($identity){
            return $decoded;
        } else {
            return $auth;
        }
    }
}