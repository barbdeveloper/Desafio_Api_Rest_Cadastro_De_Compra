<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    public function login()
    {
        $model = new UsuariosModel();
        $input = $this->request->getJSON();

        $usuario = $model->where('email', $input->email)->first();

        if (!$usuario || !password_verify($input->senha, $usuario['senha'])) {
            return $this->failUnauthorized('Email ou senha inválidos');
        }

        $key = getenv('JWT_SECRET');
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600, // Token expira em 1 hora
            'data' => [
                'id'    => $usuario['id'],
                'email' => $usuario['email'],
                'tipo'  => $usuario['tipo'],
            ]
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => 200,
            'mensagem' => 'Login realizado com sucesso',
            'token' => $token
        ]);
    }
}
