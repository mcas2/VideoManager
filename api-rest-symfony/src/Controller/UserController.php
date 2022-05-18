<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validation;
use App\Services\JwtAuth;

class UserController extends AbstractController
{
    //Este método te convierte un objeto a un json
    private function resjson($data)
    {
        //Serializar datos con servicio de Serializer
        $json = $this->get('serializer')->serialize($data, 'json');

        //Response con httpfoundation
        $response = new Response();

        //Asignar contenido a la respuesta
        $response->setContent($json);

        //Indicar formato de respuesta
        $response->headers->set('Content-Type', 'application/json');

        //Devolver respuesta
        return $response;
    }

    public function index()
    {
        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $video_repo = $this->getDoctrine()->getRepository(Video::class);
        $users = $user_repo->findAll();
        $user = $user_repo->find(1);
        $video = $video_repo->findAll();

        $data = [
            'message' => 'Welcome to your new controller',
            'path' => 'src / Contoller / UserController . php',
        ];

        return $this->resjson($data);
    }

    public function create(Request $request)
    {
        //Recoger los datos por Post
        $json = $request->get('json', null);

        //Decodificar el json
        $params = json_decode($json);

        //Hacer una respueta por defecto
        $data = [
            'status' => 'error',
            'code' => 404,
            'message' => 'El usuario no se ha creado'
        ];

        //Comprobar y validar datos
        if ($json != null) {
            $name = (!empty($params->name)) ? $params->name : null;
            $surname = (!empty($params->surname)) ? $params->surname : null;
            $email = (!empty($params->email)) ? $params->email : null;
            $password = (!empty($params->password)) ? $params->password : null;

            $validator = Validation::createValidator();
            $validate_email = $validator->validate($email, [
                new Email()
            ]);

            if (!empty($email) && count($validate_email) == 0 && !empty($password) && !empty($name) && !empty($surname)) {
                //Si la validacion es correcta, crear el objeto del usuario
                $user = new User();
                $user->setName($name);
                $user->setSurname($surname);
                $user->setEmail($email);
                $user->setRole('ROLE_USER');
                $user->setCreatedAt(new \DateTime('now'));

                //Cifrar la contraseña
                $pwd = hash('sha256', $password);
                $user->setPassword($pwd);

                //Comprobar si el usuario existe (duplicados)
                $doctrine = $this->getDoctrine();
                $em = $doctrine->getManager();
                $user_repo = $doctrine->getRepository(User::class);
                $isset_user = $user_repo->findBy(array(
                    'email' => $email
                ));

                //Si no existe, guardarlo en la BD
                if (count($isset_user) == 0) {
                    //Guardo el usuario
                    $em->persist($user);
                    $em->flush();
                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Usuario creado correctamente',
                        'user' => $user
                    ];
                } else {
                    $data = [
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'El usuario ya existe'
                    ];
                }
            }
        }
        //Hacer respuesta en json
        return $this->resjson($data);
    }

    public function login(Request $request, JwtAuth $jwt_auth)
    {
        //Recibir los datos por post
        $json = $request->get('json', null);
        $params = json_decode($json);

        //Array por defecto para devolver
        $data = [
            'status' => 'error',
            'code' => 404,
            'message' => 'El usuario no se ha podido identificar'
        ];
        //Comprobar y validar datos
        if ($json != null) {
            $email = (!empty($params->email)) ? $params->email : null;
            $password = (!empty($params->password)) ? $params->password : null;
            $getToken = (!empty($params->getToken)) ? $params->getToken : null;

            $validator = Validation::createValidator();
            $validate_email = $validator->validate($email, [
                new Email()
            ]);

            if (!empty($email) && !empty($password) && count($validate_email) == 0) {
                //Cifrar la contraseña
                $pwd = hash('sha256', $password);

                //Si todo es valido, llamaremos a un servicio para idenficar al usuario
                if ($getToken) {
                    $signup = $jwt_auth->signup($email, $pwd, $getToken);
                } else {
                    $signup = $jwt_auth->signup($email, $pwd);
                }
                return new JsonResponse($signup);
            }
        }
        //Si nos devuelve bien los datos, respuesta
        return $this->resjson($data);
    }

    public function edit(Request $request, JwtAuth $jwtAuth)
    {
        //Recoger la cabecera de autenticacion
        $token = $request->headers->get('Authorization');

        //Crear metodo para comprobar si el token es correcto
        $authCheck = $jwtAuth->checkToken($token);

        //Respuesta por defecto
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'Usuario no actualizado',
        ];

        //Si es correcto hacer la actualizacion del usuario
        if ($authCheck) {
            //Actualizar al usuario

            //Conseguir EntityManager
            $em = $this->getDoctrine()->getManager();
            //Conseguir los datos del usuario identificado
            $identity = $jwtAuth->checkToken($token, true);
            //COnseguir el usuario a actualizar completo
            $user_repo = $this->getDoctrine()->getRepository(User::class);

            $user = $user_repo->findOneBy([
                'id' => $identity->sub
            ]);

            //Recoger los datos por post
            $json = $request->get('json', null);
            $params = json_decode($json);


            //Comprobar y validar los datos
            if (!empty($json)) {
                $name = (!empty($params->name)) ? $params->name : null;
                $surname = (!empty($params->surname)) ? $params->surname : null;
                $email = (!empty($params->email)) ? $params->email : null;

                $validator = Validation::createValidator();
                $validate_email = $validator->validate($email, [
                    new Email()
                ]);

                if (!empty($email) && count($validate_email) == 0 && !empty($name) && !empty($surname)) {
                    //Asignar nuevos datos al objeto del usuario
                    $user->setEmail($email);
                    $user->setName($name);
                    $user->setSurname($surname);
                    //Comprobar los duplicados
                    $isset_user = $user_repo->findBy([
                        'email' => $email,
                    ]);

                    if (count($isset_user) == 0 || $identity->email == $email) {
                        //Guardar cambios en la BD
                        $em->persist($user);
                        $em->flush();
                        $data = [
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Usuario actualizado',
                            'user' => $user
                        ];
                    } else {
                        $data = [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'No puedes usar ese email'
                        ];
                    }
                }
            }
        }

        //...
        
        return $this->resjson($data);
    }
}
