<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UsuariosController extends ResourceController
{
    protected $modelName = 'App\Models\UsuariosModel';
    protected $format    = 'json';

    // CREATE = Criar usuário (Registro)
    public function register()
    {
        $input = $this->request->getJSON();

        if (!$input || !isset($input->nome) || !isset($input->email) || !isset($input->senha)) {
            return $this->failValidationErrors("Nome, e-mail e senha são obrigatórios.");
        }

        $usuarioModel = new UsuariosModel();

        if ($usuarioModel->where('email', $input->email)->first()) {
            return $this->failResourceExists("E-mail já cadastrado.");
        }

        $usuario = [
            'nome' => $input->nome,
            'email' => $input->email,
            'senha' => password_hash($input->senha, PASSWORD_DEFAULT),
            'tipo' => $input->tipo ?? 'cliente',
        ];

        $usuarioModel->insert($usuario);

        return $this->respondCreated([
            'cabecalho' => [
                'status' => 201,
                'mensagem' => 'Usuário cadastrado com sucesso!'
            ],
            'retorno' => ['id' => $usuarioModel->insertID()]
        ]);
    }

    // Login de usuário e geração de token JWT
    public function login()
    {
        $usuarioModel = new UsuariosModel();
        $data = $this->request->getJSON();

        if (!$data || !isset($data->email) || !isset($data->senha)) {
            return $this->failValidationErrors("E-mail e senha são obrigatórios.");
        }

        $usuario = $usuarioModel->where('email', $data->email)->first();

        if (!$usuario || !password_verify($data->senha, $usuario['senha'])) {
            return $this->failUnauthorized("E-mail ou senha incorretos.");
        }

        // Criando token JWT
        $key = "secreta";
        $payload = [
            "iat" => time(),
            "exp" => time() + 3600, // Expira em 1 hora
            "data" => [
                "id" => $usuario['id'],
                "email" => $usuario['email'],
                "tipo" => $usuario['tipo']
            ]
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Login realizado com sucesso!'
            ],
            'retorno' => [
                'token' => $token
            ]
        ]);
    }

    // READ = Listar todos os usuários (apenas para admins)
    public function index()
    {
        $model = new UsuariosModel();
        $filtros = $this->request->getGet();

        // Verifica se o usuário tem permissão para listar usuários (somente admins)
        $authHeader = $this->request->getHeader('Authorization');
        if (!$authHeader) {
            return $this->failUnauthorized("Token JWT não encontrado.");
        }

        $token = str_replace('Bearer ', '', $authHeader->getValue());
        try {
            $key = "secreta"; // Mesma chave usada no login
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            if ($decoded->data->tipo !== 'admin') {
                return $this->failForbidden("Acesso negado.");
            }
        } catch (\Exception $e) {
            return $this->failUnauthorized("Token inválido.");
        }

        // Aplica filtros dinâmicos
        if (!empty($filtros)) {
            foreach ($filtros as $campo => $valor) {
                $model->where($campo, $valor);
            }
        }

        $usuarios = $model->paginate(10);
        $paginacao = $model->pager->getDetails();

        // Removendo o campo senha dos usuários
        foreach ($usuarios as &$usuario) {
            unset($usuario['senha']);
        }

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Lista de usuários retornada com sucesso'
            ],
            'retorno' => [
                'usuarios' => $usuarios,
                'paginacao' => $paginacao
            ]
        ]);
    }

    // Buscar um usuário pelo ID (removendo senha)
    public function show($id = null)
    {
        $model = new UsuariosModel();
        $usuario = $model->find($id);

        if (!$usuario) {
            return $this->failNotFound('Usuário não encontrado');
        }

        unset($usuario['senha']); // Removendo senha da resposta

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Usuário encontrado'
            ],
            'retorno' => $usuario
        ]);
    }

    // UPDATE = Atualizar um usuário pelo ID (sem sobrescrever a senha com null)
    public function update($id = null)
    {
        $model = new UsuariosModel();
        $data = $this->request->getJSON(true);

        if (!$model->find($id)) {
            return $this->failNotFound('Usuário não encontrado');
        }

        // Se a senha não for informada, não alteramos ela
        if (isset($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        } else {
            unset($data['senha']);
        }

        $model->update($id, $data);

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Usuário atualizado com sucesso'
            ],
            'retorno' => $data
        ]);
    }

    //  DELETE = Excluir um usuário
    public function delete($id = null)
    {
        $model = new UsuariosModel();

        if (!$model->find($id)) {
            return $this->failNotFound('Usuário não encontrado');
        }

        $model->delete($id);

        return $this->respondDeleted([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Usuário excluído com sucesso'
            ]
        ]);
    }
}
