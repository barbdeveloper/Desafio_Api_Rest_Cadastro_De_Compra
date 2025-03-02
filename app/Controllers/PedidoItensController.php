<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PedidoItensController extends ResourceController
{
    protected $modelName = 'App\Models\PedidoItensModel';
    protected $format    = 'json';

    private function validarToken()
    {
        $authHeader = $this->request->getHeader('Authorization');
        if (!$authHeader) {
            return $this->failUnauthorized("Token JWT não encontrado.");
        }

        $token = str_replace('Bearer ', '', $authHeader->getValue());
        try {
            $key = "secreta";
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded->data;
        } catch (\Exception $e) {
            return $this->failUnauthorized("Token inválido.");
        }
    }

    public function index()
    {
        $user = $this->validarToken();
        if (isset($user->statusCode)) return $user;

        $filtros = $this->request->getGet();
        $this->model->where($filtros);

        $itens = $this->model->paginate(10);
        $paginacao = $this->model->pager->getDetails();

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Lista de itens do pedido retornada com sucesso'
            ],
            'retorno' => [
                'itens' => $itens,
                'paginacao' => $paginacao
            ]
        ]);
    }

    // CREATE = Inserir itens
    public function create()
    {
        $user = $this->validarToken();
        if (isset($user->statusCode)) return $user;

        $data = $this->request->getJSON(true);
        $this->model->insert($data);

        return $this->respondCreated([
            'cabecalho' => [
                'status' => 201,
                'mensagem' => 'Item do pedido cadastrado com sucesso'
            ],
            'retorno' => ['id' => $this->model->insertID()]
        ]);
    }

    // Read = ler e mostrar itens do pedido
    public function show($id = null)
    {
        $user = $this->validarToken();
        if (isset($user->statusCode)) return $user;

        $item = $this->model->find($id);
        return $item ? $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Item do pedido encontrado'
            ],
            'retorno' => $item
        ]) : $this->failNotFound('Item do pedido não encontrado');
    }

    // UPDATE  = atualizar itens do pedido
    public function update($id = null)
    {
        $user = $this->validarToken();
        if (isset($user->statusCode)) return $user;

        $data = $this->request->getJSON(true);
        $this->model->update($id, $data);

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Item do pedido atualizado com sucesso'
            ],
            'retorno' => $data
        ]);
    }

    // DELETE = EXCLUIR ITENS
    public function delete($id = null)
    {
        $user = $this->validarToken();
        if (isset($user->statusCode)) return $user;

        if (!$this->model->find($id)) {
            return $this->failNotFound('Item do pedido não encontrado');
        }

        $this->model->delete($id);
        return $this->respondDeleted([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Item do pedido deletado com sucesso'
            ]
        ]);
    }
}
