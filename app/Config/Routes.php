<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Rotas públicas (sem autenticação)
$routes->post('usuarios/login', 'UsuariosController::login'); // Login
$routes->post('usuarios/registrar', 'UsuariosController::register'); // Registro de usuário

// Grupo protegido por autenticação JWT
$routes->group('', ['filter' => 'jwt'], function ($routes) {

    // Rotas para Usuários (CRUD protegido)
    $routes->group('usuarios', function ($routes) {
        $routes->get('/', 'UsuariosController::index');
        $routes->get('(:num)', 'UsuariosController::show/$1');
        $routes->put('(:num)', 'UsuariosController::update/$1');
        $routes->delete('(:num)', 'UsuariosController::delete/$1');
    });

    // Rotas para Clientes (CRUD protegido)
    $routes->group('clientes', function ($routes) {
        $routes->get('/', 'ClientesController::index');
        $routes->post('/', 'ClientesController::create');
        $routes->get('(:num)', 'ClientesController::show/$1');
        $routes->put('(:num)', 'ClientesController::update/$1');
        $routes->delete('(:num)', 'ClientesController::delete/$1');
    });

    // Rotas para Pedidos (CRUD protegido)
    $routes->group('pedidos', function ($routes) {
        $routes->get('/', 'PedidosController::index');
        $routes->post('/', 'PedidosController::create');
        $routes->get('(:num)', 'PedidosController::show/$1');
        $routes->put('(:num)', 'PedidosController::update/$1');
        $routes->delete('(:num)', 'PedidosController::delete/$1');
    });

    // Rotas para Itens do Pedido (CRUD protegido)
    $routes->group('pedido_itens', function ($routes) {
        $routes->get('/', 'PedidoItensController::index');
        $routes->post('/', 'PedidoItensController::create');
        $routes->get('(:num)', 'PedidoItensController::show/$1');
        $routes->put('(:num)', 'PedidoItensController::update/$1');
        $routes->delete('(:num)', 'PedidoItensController::delete/$1');
    });

    // Rotas para Produtos (CRUD protegido)
    $routes->group('produtos', function ($routes) {
        $routes->get('/', 'ProdutosController::index');
        $routes->post('/', 'ProdutosController::create');
        $routes->get('(:num)', 'ProdutosController::show/$1');
        $routes->put('(:num)', 'ProdutosController::update/$1');
        $routes->delete('(:num)', 'ProdutosController::delete/$1');
    });

});
