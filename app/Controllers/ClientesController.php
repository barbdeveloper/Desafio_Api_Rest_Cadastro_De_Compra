<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ClientesModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ClientesController extends ResourceController
{
    protected $modelName = 'App\Models\ClientesModel';
    protected $format    = 'json';

    // CREATE = Criar um novo cliente
    public function create()
    {
        if (!$this->validateJWT()) {
            return $this->failUnauthorized("Token inválido ou ausente.");
        }

        $data = $this->request->getJSON(true);

        if (!isset($data['cpf_cnpj']) || !isset($data['nome_razao'])) {
            return $this->failValidationErrors("CPF/CNPJ e Nome/Razão Social são obrigatórios.");
        }

        // Verifica se o CPF/CNPJ já está cadastrado
        if ($this->model->where('cpf_cnpj', $data['cpf_cnpj'])->first()) {
            return $this->failResourceExists("Este CPF/CNPJ já está cadastrado.");
        }

        if (!$this->model->insert($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated([
            'cabecalho' => [
                'status' => 201,
                'mensagem' => 'Cliente cadastrado com sucesso'
            ],
            'retorno' => ['id' => $this->model->insertID()]
        ]);
    }

    // READ = Listar clientes com paginação e filtros
    public function index()
    {
        if (!$this->validateJWT()) {
            return $this->failUnauthorized("Token inválido ou ausente.");
        }

        $model = new ClientesModel();
        $filtros = $this->request->getGet();

        // Aplicando filtros dinâmicos
        if (!empty($filtros)) {
            foreach ($filtros as $campo => $valor) {
                $model->where($campo, $valor);
            }
        }

        $clientes = $model->paginate(10);
        $paginacao = $model->pager->getDetails();

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Lista de clientes retornada com sucesso'
            ],
            'retorno' => [
                'clientes' => $clientes,
                'paginacao' => $paginacao
            ]
        ]);
    }

    // Buscar um cliente pelo ID
    public function show($id = null)
    {
        if (!$this->validateJWT()) {
            return $this->failUnauthorized("Token inválido ou ausente.");
        }

        $cliente = $this->model->find($id);

        if (!$cliente) {
            return $this->failNotFound('Cliente não encontrado');
        }

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente encontrado'
            ],
            'retorno' => $cliente
        ]);
    }

    // UPDATE = Atualizar um cliente pelo ID
    public function update($id = null)
    {
        if (!$this->validateJWT()) {
            return $this->failUnauthorized("Token inválido ou ausente.");
        }

        $data = $this->request->getJSON(true);

        if (!$this->model->find($id)) {
            return $this->failNotFound('Cliente não encontrado');
        }

        if (!$this->model->update($id, $data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente atualizado com sucesso'
            ],
            'retorno' => $data
        ]);
    }

    // DELETE = Excluir um cliente
    public function delete($id = null)
    {
        if (!$this->validateJWT()) {
            return $this->failUnauthorized("Token inválido ou ausente.");
        }

        if (!$this->model->find($id)) {
            return $this->failNotFound('Cliente não encontrado');
        }

        if (!$this->model->delete($id)) {
            return $this->fail("Falha ao excluir o cliente.");
        }

        return $this->respondDeleted([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente excluído com sucesso'
            ]
        ]);
    }

    // Validação do token JWT
    private function validateJWT()
    {
        $authHeader = $this->request->getHeader('Authorization');
        if (!$authHeader) {
            return false;
        }

        $token = str_replace('Bearer ', '', $authHeader->getValue());

        try {
            $key = "secreta"; // Chave secreta usada no login
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Função para validar CPF/CNPJ
    private function validarCpfCnpj($valor) {
        $valor = preg_replace('/\D/', '', $valor);
        if (strlen($valor) == 11) {
            return $this->validarCpf($valor);
        } elseif (strlen($valor) == 14) {
            return $this->validarCnpj($valor);
        }
        return false;
    }

    // Validação de CPF
    private function validarCpf($cpf) {
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        return true;
    }

    // Validação de CNPJ
    private function validarCnpj($cnpj) {
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) return false;
        $tamanho = [5, 6];
        $multiplicador = [
            [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
            [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
        ];
        for ($j = 0; $j < 2; $j++) {
            for ($i = 0, $soma = 0; $i < $tamanho[$j] + 8; $i++) {
                $soma += $cnpj[$i] * $multiplicador[$j][$i];
            }
            $digito = ($soma % 11) < 2 ? 0 : 11 - ($soma % 11);
            if ($cnpj[$tamanho[$j] + 8] != $digito) return false;
        }
        return true;
    }
}
