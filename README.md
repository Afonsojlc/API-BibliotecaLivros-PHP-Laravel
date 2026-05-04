
# 📚 API - Biblioteca Digital (Laravel)

> Uma API RESTful profissional para gestão de bibliotecas, com autenticação JWT (Sanctum), controlo de acessos (RBAC) e base de dados SQLite.

<p align="left">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php" alt="PHP" />
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel" />
  <img src="https://img.shields.io/badge/SQLite-Database-003B57?style=for-the-badge&logo=sqlite" alt="SQLite" />
  <img src="https://img.shields.io/badge/Postman-Testing-FF6C37?style=for-the-badge&logo=postman" alt="Postman" />
</p>

---

## 📖 Sobre o Projeto

Este projeto foi desenvolvido para demonstrar a aplicação de boas práticas em **Backend com Laravel**. Consiste num sistema de gestão para uma biblioteca digital, focado na arquitetura MVC, segurança, otimização de queries de base de dados e design de endpoints limpos.

### ✨ Principais Características
* **Autenticação e Segurança:** Implementação de tokens API via `Laravel Sanctum`.
* **Controlo de Acessos (RBAC):** Middleware customizado para separar permissões entre `Admin` e `Leitor`.
* **Otimização de Base de Dados:** Uso de *Eager Loading* (`with`) para evitar o problema de queries N+1.
* **Validações:** Validação estrita de todos os dados recebidos nos *Requests*.
* **Relacionamentos Complexos:** Utilização nativa do Eloquent para gerir tabelas pivot (`belongsToMany`).

---

## 🚀 Como Executar Localmente

### Pré-requisitos
* PHP 8.1 ou superior instalado na máquina.
* [Composer](https://getcomposer.org/) instalado.

### Passos de Instalação

**1. Clonar o repositório**
```bash
git clone [https://github.com/Afonsojlc/API-BibliotecaLivros-PHP-Laravel.git](https://github.com/Afonsojlc/API-BibliotecaLivros-PHP-Laravel.git)
cd API-BibliotecaLivros-PHP-Laravel
```

**2\. Instalar dependências**



```Bash
composer install
```

**3\. Configurar Variáveis de Ambiente**



```Bash
cp .env.example .env
```

*No ficheiro `.env`, certifique-se que a base de dados está configurada para SQLite:*



```Fragmento do código
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

**4\. Preparar Base de Dados e Gerar Chave**



```Bash
php artisan key:generate
touch database/database.sqlite # Em Windows PowerShell use: New-Item -ItemType File database/database.sqlite
php artisan migrate:fresh --seed
```

**5\. Iniciar o Servidor**

```Bash
php artisan serve
```

A API ficará disponível em `http://127.0.0.1:8000`.

* * * * *

🔐 Credenciais de Teste (Seeders)
---------------------------------

A base de dados é automaticamente populada com dados fictícios para facilitar os testes. Pode utilizar os seguintes acessos:

| **Tipo de Perfil** | **Email** | **Password** | **Permissões** |
| --- | --- | --- | --- |
| **Administrador** | `admin@biblioteca.pt` | `admin123` | Acesso total a todas as rotas (CRUD completo). |
| **Leitor** | `joao@exemplo.pt` | `leitor123` | Consulta de catálogo e gestão das próprias reservas. |

* * * * *

📡 Referência da API (Endpoints)
--------------------------------

> **Dica:** Na pasta `Postman/` do repositório, encontrará o ficheiro `API-BibliotecaLivros-PHP&Laravel.postman_collection.json` pronto a importar para testar a API de forma rápida e intuitiva.

### 🔑 Autenticação

| **Método** | **Endpoint** | **Acesso** | **Descrição** |
| --- | --- | --- | --- |
| `POST` | `/api/register` | Público | Regista um novo leitor |
| `POST` | `/api/login` | Público | Autentica e retorna o *Bearer Token* |
| `POST` | `/api/logout` | Autenticado | Invalida o token atual |
| `GET` | `/api/me` | Autenticado | Retorna os dados do utilizador logado |

### 📚 Livros e Autores

| **Método** | **Endpoint** | **Acesso** | **Descrição** |
| --- | --- | --- | --- |
| `GET` | `/api/livros` | Autenticado | Lista todos os livros (suporta filtros na URL) |
| `GET` | `/api/livros/{id}` | Autenticado | Detalhes de um livro específico |
| `POST` | `/api/livros` | **Admin** | Adiciona um novo livro ao catálogo |
| `GET` | `/api/autores` | Autenticado | Lista todos os autores |
| `POST` | `/api/autores` | **Admin** | Adiciona um novo autor |

*(Nota: Rotas `PUT` e `DELETE` para Livros e Autores também estão implementadas e são exclusivas para Administradores).*

### 📅 Reservas

| **Método** | **Endpoint** | **Acesso** | **Descrição** |
| --- | --- | --- | --- |
| `POST` | `/api/reservas` | **Leitor** | Efetua uma nova reserva de livros |
| `GET` | `/api/reservas/minhas` | **Leitor** | Lista as reservas do utilizador autenticado |
| `GET` | `/api/reservas` | **Admin** | Lista o histórico de todas as reservas globais |
| `PATCH` | `/api/reservas/{id}` | **Admin** | Atualiza o estado da reserva (Pendente, Ativa, Devolvida) |

* * * * *

👨‍💻 Autor
-----------

Desenvolvido por **Afonso Carvalho**