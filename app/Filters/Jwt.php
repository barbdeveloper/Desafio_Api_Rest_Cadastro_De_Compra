<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Jwt implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeader("Authorization");

        if (!$header) {
            return Services::response()->setJSON(['cabecalho' => ['status' => 401, 'mensagem' => 'Token não fornecido']])->setStatusCode(401);
        }

        $token = str_replace("Bearer ", "", $header->getValue());
        $key = "secreta";

        try {
            JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            return Services::response()->setJSON(['cabecalho' => ['status' => 401, 'mensagem' => 'Token inválido']])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada aqui
    }
}
