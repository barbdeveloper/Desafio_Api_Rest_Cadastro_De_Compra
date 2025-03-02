<?php

namespace App\Models;
use CodeIgniter\Model;

class ProdutosModel extends Model {
    protected $table = 'produtos';
    protected $primaryKey = 'id';
    protected $allowedFields = [ 'nome', 'preco', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
