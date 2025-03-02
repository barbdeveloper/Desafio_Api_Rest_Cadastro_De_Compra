<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProdutosModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ProdutosController extends ResourceController {
    protected $modelName = 'App\Models\ProdutosModel';
    protected $format    = 'json';

    // Middleware para verificar JWT
    private function verificarToken() {
        $authHeader = $this->request->getHeader('Authorization');
        if (!$authHeader) {
            return $this->failUnauthorized("Token JWT não encontrado.");
        }

        $token = str_replace('Bearer ', '', $authHeader->getValue());
        try {
            $key = "secreta";
            return JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            return $this->failUnauthorized("Token inválido.");
        }
    }

    // CREATE = Criar produto
    public function create() {
        $auth = $this->verificarToken();
        if (isset($auth->statusCode)) return $auth;

        $data = $this->request->getJSON(true);

        // Validação de CPF/CNPJ
        if (!isset($data['cpf_cnpj']) || !$this->validarCpfCnpj($data['cpf_cnpj'])) {
            return $this->failValidationErrors("CPF/CNPJ inválido.");
        }

        if (!$this->model->insert($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated(['mensagem' => 'Produto cadastrado com sucesso']);
    }

    //  READ = Listar produtos com paginação e filtros
    public function index() {
        $auth = $this->verificarToken();
        if (isset($auth->statusCode)) return $auth;

        $filtros = $this->request->getGet();
        $model = new ProdutosModel();

        if (!empty($filtros)) {
            foreach ($filtros as $campo => $valor) {
                $model->where($campo, $valor);
            }
        }

        $produtos = $model->paginate(10);
        $paginacao = $model->pager->getDetails();

        return $this->respond([
            'cabecalho' => ['status' => 200, 'mensagem' => 'Lista de produtos'],
            'retorno' => ['produtos' => $produtos, 'paginacao' => $paginacao]
        ]);
    }

    // Buscar produto por ID
    public function show($id) {
        $auth = $this->verificarToken();
        if (isset($auth->statusCode)) return $auth;

        $produto = $this->model->find($id);
        return $produto ? $this->respond(['cabecalho' => ['status' => 200], 'retorno' => $produto]) : $this->failNotFound('Produto não encontrado');
    }

    // UPDATE = Atualizar produto
    public function update($id) {
        $auth = $this->verificarToken();
        if (isset($auth->statusCode)) return $auth;

        $data = $this->request->getJSON(true);

        if (!$this->model->update($id, $data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respond(['mensagem' => 'Produto atualizado com sucesso']);
    }

    // DELETE = Deletar produto com verificação
    public function delete($id) {
        $auth = $this->verificarToken();
        if (isset($auth->statusCode)) return $auth;

        $produto = $this->model->find($id);
        if (!$produto) {
            return $this->failNotFound('Produto não encontrado');
        }

        if (!$this->model->delete($id)) {
            return $this->fail('Falha ao excluir o produto');
        }

        return $this->respondDeleted(['mensagem' => 'Produto excluído com sucesso']);
    }

}
