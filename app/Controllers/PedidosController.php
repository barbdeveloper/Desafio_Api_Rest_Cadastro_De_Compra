<?php

namespace App\Controllers;

use App\Models\PedidosModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PedidosController extends ResourceController
{
    protected $modelName = 'App\Models\PedidosModel';
    protected $format    = 'json';

    private function validarToken()
    {
        $authHeader = $this->request->getHeader('Authorization');
        if (!$authHeader) {
            return $this->failUnauthorized("Token JWT não encontrado.");
        }

        $token = str_replace('Bearer ', '', $authHeader->getValue());
        try {
            $key = "secreta"; // Chave usada na geração do token
            return JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            return $this->failUnauthorized("Token inválido ou expirado.");
        }
    }

    // LISTAR TODOS OS PEDIDOS COM PAGINAÇÃO E FILTROS
    public function index()
    {
        $auth = $this->validarToken();
        if (!is_object($auth)) return $auth;

        $model = new PedidosModel();
        $filtros = $this->request->getGet();

        if (!empty($filtros)) {
            foreach ($filtros as $campo => $valor) {
                $model->where($campo, $valor);
            }
        }

        $pedidos = $model->paginate(10);
        $paginacao = $model->pager->getDetails();

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Lista de pedidos retornada com sucesso'
            ],
            'retorno' => [
                'pedidos' => $pedidos,
                'paginacao' => $paginacao
            ]
        ]);
    }

    // CREATE = CRIAR UM NOVO PEDIDO
    public function create()
    {
        $auth = $this->validarToken();
        if (!is_object($auth)) return $auth;

        $data = $this->request->getJSON(true);
        if (!isset($data['cliente_id'], $data['status'], $data['data_pedido'])) {
            return $this->failValidationErrors("Campos obrigatórios: cliente_id, status e data_pedido.");
        }

        // Validação de status permitido
        $statusesValidos = ['Em Aberto', 'Pago', 'Cancelado'];
        if (!in_array($data['status'], $statusesValidos)) {
            return $this->failValidationErrors("Status inválido. Os valores permitidos são: 'Em Aberto', 'Pago' ou 'Cancelado'.");
        }

        $this->model->insert($data);
        return $this->respondCreated([
            'cabecalho' => [
                'status' => 201,
                'mensagem' => 'Pedido criado com sucesso'
            ],
            'retorno' => ['id' => $this->model->insertID()]
        ]);
    }

    // READ = BUSCAR UM PEDIDO PELO ID
    public function show($id = null)
    {
        $auth = $this->validarToken();
        if (!is_object($auth)) return $auth;

        $pedido = $this->model->find($id);
        if (!$pedido) {
            return $this->failNotFound('Pedido não encontrado');
        }

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedido encontrado'
            ],
            'retorno' => $pedido
        ]);
    }

    // UPDATE = ATUALIZAR PEDIDO
    public function update($id = null)
    {
        $auth = $this->validarToken();
        if (!is_object($auth)) return $auth;

        if (!$this->model->find($id)) {
            return $this->failNotFound('Pedido não encontrado');
        }

        $data = $this->request->getJSON(true);

        // Validação de status permitido
        if (isset($data['status'])) {
            $statusesValidos = ['Em Aberto', 'Pago', 'Cancelado'];
            if (!in_array($data['status'], $statusesValidos)) {
                return $this->failValidationErrors("Status inválido. Os valores permitidos são: 'Em Aberto', 'Pago' ou 'Cancelado'.");
            }
        }

        $this->model->update($id, $data);

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedido atualizado com sucesso'
            ],
            'retorno' => $data
        ]);
    }

    // DELETE = EXCLUIR PEDIDO
    public function delete($id = null)
    {
        $auth = $this->validarToken();
        if (!is_object($auth)) return $auth;

        if (!$this->model->find($id)) {
            return $this->failNotFound('Pedido não encontrado');
        }

        $this->model->delete($id);
        return $this->respondDeleted([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedido excluído com sucesso'
            ]
        ]);
    }
}
