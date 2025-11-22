# Aiqfome API

API REST desenvolvida em Laravel 12 para gerenciamento de clientes e produtos favoritos, com autenticação JWT e integração com API externa de produtos.

## 📋 Requisitos

- Docker e Docker Compose
- Git
- Linux/macOS (para usar o script `start.sh`) ou Windows (executar comandos manualmente)

## 🚀 Como Rodar o Projeto

### Opção 1: Linux/macOS (Script Automatizado)

1. Clone o repositório:
```bash
git clone git@github.com:felipesilva4/desafio-aiqfome.git
cd desafio-aiqfome
```

2. Execute o script de inicialização:
```bash
chmod +x start.sh
./start.sh
```

O script irá:
- Copiar `.env.example` para `.env`
- Subir os containers Docker (app, nginx, postgres, swagger)
- Ajustar permissões da pasta `storage`
- Instalar dependências do Composer
- Gerar a chave da aplicação
- Executar as migrations e seeders

### Opção 2: Windows ou Execução Manual

1. Clone o repositório:
```bash
git clone git@github.com:felipesilva4/desafio-aiqfome.git
cd desafio-aiqfome
```

2. Copie o arquivo de ambiente:
```bash
copy .env.example .env
```

3. Suba os containers:
```bash
docker compose up -d --build
```

4. Ajuste as permissões (Linux/macOS):
```bash
sudo chmod -R 777 storage
```

5. Instale as dependências:
```bash
docker exec -u 0 -it app-laravel composer install
```

6. Gere a chave da aplicação:
```bash
docker exec -u 0 -it app-laravel php artisan key:generate
```

7. Execute as migrations e seeders:
```bash
docker exec -it app-laravel php artisan migrate --seed
```

## 🌐 Acessos

Após iniciar o projeto, os serviços estarão disponíveis em:

- **API**: http://localhost:8000/api
- **Swagger UI**: http://localhost:8000/api/documentation
- **Swagger Editor**: http://localhost:8081
- **PostgreSQL**: localhost:5432

## 🔐 Credenciais Padrão

Após executar o seeder, você terá um usuário admin:

- **Email**: `admin@admin.com`
- **Senha**: `admin`

## 💌 Colection do postman

[![Postman Collection](https://img.shields.io/badge/postman-collection-2578b4.svg)](https://github.com/felipesilva4/desafio-aiqfome/blob/main/aiqfome.postman_collection.json)

Eu inseri o postman collection para facilitar a utilização do projeto, você pode acessá-lo [aqui](https://github.com/felipesilva4/desafio-aiqfome)

Porém será necessário fazer se autenticar e inserir o token no header da requisição.

## 📚 Endpoints da API

### Autenticação

#### POST `/api/login`
Autentica um usuário e retorna um token JWT.

**Request:**
```json
{
  "email": "admin@admin.com",
  "password": "admin"
}
```

**Response:**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### Clientes

Todos os endpoints de clientes requerem autenticação JWT. Inclua o token no header:
```
Authorization: Bearer {seu_token}
```

#### POST `/api/clients`
Cria um novo cliente.

**Request:**
```json
{
  "name": "João Silva",
  "email": "joao@example.com"
}
```

#### GET `/api/clients`
Lista todos os clientes.

#### GET `/api/clients/{id}`
Busca um cliente específico com seus produtos favoritos.

#### PUT/PATCH `/api/clients/{id}`
Atualiza os dados de um cliente.

**Request:**
```json
{
  "name": "João Silva Atualizado",
  "email": "joao.novo@example.com"
}
```

#### DELETE `/api/clients/{id}`
Remove um cliente do sistema.

### Produtos Favoritos

#### POST `/api/clients/{id}/favorite-products`
Adiciona um produto à lista de favoritos de um cliente.

**Request:**
```json
{
  "product_id": 1
}
```

**Response (201 Created):**
```json
{
  "id": 1,
  "client_id": 1,
  "product_id": 1
}
```

#### GET `/api/clients/{id}/favorite-products`
Lista todos os produtos favoritos de um cliente.

#### DELETE `/api/clients/{id}/favorite-products/{product_id}`
Remove um produto específico da lista de favoritos de um cliente.

**Response (204 No Content):**
```
(sem conteúdo)
```

**Response:**
```json
[
  {
    "id": 1,
    "titulo": "Produto Exemplo",
    "imagem": "https://example.com/image.jpg",
    "preco": 99.90,
    "descricao": "Descrição do produto"
  }
]
```

## 📖 Documentação Swagger

A documentação completa da API está disponível em:

- **Swagger UI**: http://localhost:8000/api/documentation
- **Swagger Editor**: http://localhost:8081

Para regenerar a documentação após alterações:
```bash
docker exec -it app-laravel php artisan l5-swagger:generate
```

## 🛠️ Tecnologias Utilizadas

- **Laravel 12**: Framework PHP
- **PostgreSQL 16**: Banco de dados
- **JWT Auth**: Autenticação via tokens
- **Docker & Docker Compose**: Containerização
- **Nginx**: Servidor web
- **Swagger/OpenAPI**: Documentação da API
- **Guzzle HTTP**: Cliente HTTP para APIs externas

## 📁 Estrutura do Projeto

```
app/
├── DTOs/                    # Data Transfer Objects
├── Exceptions/              # Exceções customizadas
├── Http/
│   ├── Controllers/         # Controladores da API
│   └── Middleware/          # Middlewares
├── Models/                  # Modelos Eloquent
├── Providers/               # Service Providers
├── Repositories/            # Repositórios (Repository Pattern)
└── Services/                # Serviços de negócio

database/
├── migrations/              # Migrations do banco
└── seeders/                 # Seeders para dados iniciais

routes/
└── api.php                  # Rotas da API
```
## ⚙️ Configuração

### Variáveis de Ambiente

O arquivo `.env` contém as configurações principais:

Porém não é neecssário configuração, pois inseri elas no env do container

```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=root
DB_PASSWORD=root
```

## 📝 Códigos de Status HTTP

- `200` - Sucesso
- `201` - Criado com sucesso
- `401` - Não autenticado
- `404` - Recurso não encontrado
- `409` - Conflito (ex: email já cadastrado, produto já favoritado)
- `422` - Dados inválidos

## 🏗️ Arquitetura

O projeto segue os princípios SOLID e utiliza:

- **Repository Pattern**: Abstração do acesso a dados
- **Service Layer**: Lógica de negócio isolada
- **DTOs**: Transferência de dados padronizada
- **Dependency Injection**: Inversão de dependências
- **Interface Segregation**: Interfaces específicas e coesas

## 📄 Licença

Este projeto é um desafio técnico desenvolvido para avaliação.

## 👤 Autor

Desenvolvido como parte de um desafio técnico.

---

**Nota**: Certifique-se de ter Docker e Docker Compose instalados antes de iniciar o projeto.
