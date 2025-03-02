<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoItensModel extends Model
{
    protected $table = 'pedido_itens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pedido_id', 'produto_id', 'quantidade', 'preco_unitario'];

    // Regras de validação
    protected $validationRules = [
        'pedido_id'    => 'required|integer|is_not_unique[pedidos.id]',
        'produto_id'   => 'required|integer|is_not_unique[produtos.id]',
        'quantidade'   => 'required|integer|greater_than[0]',
        'preco_unitario' => 'required|decimal'
    ];

    // Mensagens de erro personalizadas
    protected $validationMessages = [
        'pedido_id' => [
            'required'     => 'O campo pedido_id é obrigatório.',
            'integer'      => 'O campo pedido_id deve ser um número inteiro.',
            'is_not_unique' => 'O pedido informado não existe.'
        ],
        'produto_id' => [
            'required'     => 'O campo produto_id é obrigatório.',
            'integer'      => 'O campo produto_id deve ser um número inteiro.',
            'is_not_unique' => 'O produto informado não existe.'
        ],
        'quantidade' => [
            'required'     => 'O campo quantidade é obrigatório.',
            'integer'      => 'O campo quantidade deve ser um número inteiro.',
            'greater_than' => 'A quantidade deve ser maior que zero.'
        ],
        'preco_unitario' => [
            'required' => 'O campo preço unitário é obrigatório.',
            'decimal'  => 'O campo preço unitário deve ser um número decimal.'
        ]
    ];

    // Método para buscar os itens do pedido com informações dos produtos
    public function getItensComProdutos($pedidoId)
    {
        return $this->select('pedido_itens.*, produtos.nome as produto_nome')
            ->join('produtos', 'produtos.id = pedido_itens.produto_id')
            ->where('pedido_itens.pedido_id', $pedidoId)
            ->findAll();
    }
}
