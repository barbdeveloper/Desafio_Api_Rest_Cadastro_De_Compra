<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $table = 'usuarios'; // Nome da tabela no banco de dados
    protected $primaryKey = 'id'; // Nome da chave primária
    protected $allowedFields = ['nome', 'email', 'senha', 'tipo', 'created_at','updated_at' ]; // Campos permitidos para inserção/atualização
}
