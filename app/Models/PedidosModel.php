<?php

namespace App\Models;
use CodeIgniter\Model;

class PedidosModel extends Model {
    protected $table = 'pedidos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['cliente_id', 'status', 'data_pedido'];
    protected $useTimestamps = true;
}
