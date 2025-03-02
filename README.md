## Desafio API REST - Análise e Desenvolvimento de Sistemas
Este repositório contém a implementação de uma API REST para cadastro de pedidos de compra. A API foi desenvolvida utilizando o framework CodeIgniter 4 (PHP) e um banco de dados MySQL. O desafio inclui funcionalidades para manipulação de dados de clientes, produtos, pedidos e usuários, através de endpoints de CRUD (Create, Read, Update, Delete).

**Tecnologias Utilizadas**
•	PHP 7.4 ou superior
•	CodeIgniter 4
•	MySQL
•	JWT (JSON Web Token) para autenticação
•	Postman para testar os endpoints

**Funcionalidades da API**
A API possui os seguintes endpoints para gerenciamento de dados:

1. Clientes
•	POST /clientes - Criar um novo cliente.
•	GET /clientes - Listar todos os clientes.
•	GET /clientes/{id} - Obter informações de um cliente específico.
•	PUT /clientes/{id} - Atualizar informações de um cliente.
•	DELETE /clientes/{id} - Deletar um cliente.
2. Produtos
•	POST /produtos - Criar um novo produto.
•	GET /produtos - Listar todos os produtos.
•	GET /produtos/{id} - Obter informações de um produto específico.
•	PUT /produtos/{id} - Atualizar informações de um produto.
•	DELETE /produtos/{id} - Deletar um produto.
3. Pedidos
•	POST /pedidos - Criar um novo pedido.
•	GET /pedidos - Listar todos os pedidos.
•	GET /pedidos/{id} - Obter informações de um pedido específico.
•	PUT /pedidos/{id} - Atualizar informações de um pedido.
•	DELETE /pedidos/{id} - Deletar um pedido.
4. Pedido Itens
•	POST /pedido_itens - Criar um novo item de pedido.
•	GET /pedido_itens - Listar todos os itens de pedido.
•	GET /pedido_itens/{id} - Obter informações de um item de pedido específico.
•	PUT /pedido_itens/{id} - Atualizar informações de um item de pedido.
•	DELETE /pedido_itens/{id} - Deletar um item de pedido.
5. Usuários
•	POST /usuarios/register - Registrar um novo usuário.
•	POST /usuarios/login - Realizar login e obter um token JWT.

**Estrutura do Projeto**
O projeto foi estruturado com base na arquitetura MVC (Model-View-Controller). A seguir, uma breve explicação de cada pasta:
•	app/Controllers: Contém os controladores da API, responsáveis pela lógica de manipulação das requisições.
o	Exemplo: ClientesController, ProdutosController, PedidosController, PedidosItensController, UsuariosController.
•	app/Models: Contém os models, responsáveis pela interação com o banco de dados.
o	Exemplo: ClientesModel, ProdutosModel, PedidosModel, UsuariosModel.
•	app/Filters: Contém filtros para controle de acesso, como a autenticação com JWT.
o	Exemplo: Jwt.php - Responsável por verificar o token JWT nas rotas protegidas.
•	app/Config: Contém configurações gerais do projeto, como as configurações de filtros e rotas.
o	Exemplo: Filters.php - Configura os filtros de autenticação JWT.

**Como Rodar o Projeto**
Pré-requisitos
•	PHP 7.4 ou superior
•	Composer (para instalar dependências)
•	MySQL
•	Postman (para testar os endpoints)

**Passos para execução**
1.	Clone o repositório: git clone https://github.com/seu_usuario/seu_repositorio.git
cd seu_repositorio

2.	Instale as dependências: composer install

3.	Configure o banco de dados: No arquivo .env, altere as configurações de conexão com o banco de dados para refletir os dados do seu ambiente.

4.	Crie o banco de dados e as tabelas: O banco de dados já foi configurado para ser criado usando as migrations do CodeIgniter. Para rodar as migrations, execute: php spark migrate
5.	Execute o servidor local: Inicie o servidor embutido do PHP com o comando:
php spark serve
6.	Testando com Postman:
o	Registre um usuário (POST /usuarios/register).
o	Faça login (POST /usuarios/login) e obtenha o token JWT.
o	Use esse token para testar os endpoints de CRUD (clientes, produtos, pedidos, etc.), incluindo o token no cabeçalho das requisições.

**Como Funciona a Autenticação JWT**
A autenticação na API é feita utilizando JSON Web Tokens (JWT). 
Esse projeto foi muito importante para mim, pois **foi a primeira vez que realizei a implementação de token**
Após o login, o usuário recebe um token que deve ser enviado no cabeçalho das requisições para acessar rotas protegidas. Exemplo de cabeçalho no Postman: 
Authorization: Bearer {seu_token_jwt}

**Considerações Finais**
Esse é um projeto desenvolvido com o objetivo de simular a implementação de uma API REST para cadastro de pedidos de compra, utilizando boas práticas de desenvolvimento e arquitetura MVC.
Apesar de ser minha primeira vez implementando uma API, consegui estruturar o projeto de forma que as funcionalidades de CRUD para os principais recursos (clientes, produtos, pedidos) funcionem corretamente, com a segurança fornecida pelo JWT.

**Contribuições**
Este projeto foi desenvolvido como parte do meu desafio acadêmico. Caso queira contribuir ou sugerir melhorias, fique à vontade para abrir uma issue ou enviar um pull request.
