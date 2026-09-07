# Programação Web — Projeto Backend
# 📚 API de Gestão de Biblioteca com Laravel + SQLite
**Manual do Aluno | 1.º Ano **

---

## Introdução

Já trabalhaste com APIs em Node.js. Agora vais construir uma API equivalente mas com Laravel — uma das frameworks PHP mais populares e utilizadas em contexto profissional. Vais perceber que a lógica é muito semelhante: rotas, controllers, modelos, autenticação por token e testes com Postman.

O projeto consiste em construir uma **API REST para uma Biblioteca Digital**, onde os utilizadores podem gerir livros, autores e reservas, com diferentes níveis de acesso (administrador e leitor).

> 📝 **NOTA:** Este manual guia-te passo a passo, mas não o copies cegamente. Lê cada secção, percebe o que faz e porquê — as questões de compreensão no final de cada fase ajudam-te a consolidar o conhecimento.

---

## Objetivos de Aprendizagem

- Configurar um projeto Laravel com base de dados SQLite sem instalar servidores
- Compreender a estrutura MVC do Laravel e como se compara ao Express
- Criar modelos Eloquent com relações (hasMany, belongsTo, belongsToMany)
- Implementar autenticação por token com Laravel Sanctum
- Construir endpoints CRUD completos com validação de inputs
- Aplicar roles e permissões (administrador / leitor)
- Testar todos os endpoints com Postman

---

## Comparação: Node.js vs Laravel

| Conceito | Node.js (Express) | Laravel |
|---|---|---|
| Rotas | routes/index.js + app.use() | routes/api.php |
| Controller | Função ou classe separada | php artisan make:controller |
| Modelo / ORM | Mongoose / Sequelize | Eloquent (php artisan make:model) |
| Migrações | Scripts manuais ou Knex | php artisan migrate |
| Autenticação | jsonwebtoken (JWT) | Laravel Sanctum (tokens) |
| Validação | express-validator | FormRequest / validate() |
| Servidor dev | node index.js / nodemon | php artisan serve |

---

# Fase 1 — Configuração do Projeto

## 1.1 Pré-requisitos

Antes de começar, verifica se tens o PHP instalado:

```bash
php --version
# Necessário: PHP 8.1 ou superior
```

Descarrega o Composer portátil (sem necessidade de instalar):

```bash
# Descarregar o Composer sem instalar
php -r "copy('https://getcomposer.org/composer.phar', 'composer.phar');"

# Verificar
php composer.phar --version
```

## 1.2 Criar o Projeto Laravel

```bash
# Criar o projeto
php composer.phar create-project laravel/laravel biblioteca-api

# Entrar na pasta
cd biblioteca-api
```

## 1.3 Configurar a Base de Dados SQLite

O SQLite guarda tudo num único ficheiro — não precisas de instalar nem configurar nenhum servidor de base de dados.

Abre o ficheiro `.env` na raiz do projeto e substitui a secção de base de dados:

```env
# .env — secção de base de dados
DB_CONNECTION=sqlite
DB_DATABASE=/caminho/absoluto/para/biblioteca-api/database/database.sqlite

# Comenta ou remove estas linhas:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_USERNAME=root
# DB_PASSWORD=
```

Cria o ficheiro da base de dados:

```bash
# Windows (PowerShell)
New-Item -ItemType File database/database.sqlite

# macOS / Linux
touch database/database.sqlite
```

> 💡 **DICA:** No Laravel 11+, podes simplificar o DB_DATABASE para apenas `database/database.sqlite`. O Laravel resolve o caminho automaticamente a partir da raiz do projeto.

## 1.4 Instalar o Laravel Sanctum

O Sanctum é o pacote oficial do Laravel para autenticação por token — equivalente ao jsonwebtoken que usaste em Node.js.

```bash
php composer.phar require laravel/sanctum

# Publicar as configurações do Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

## 1.5 Estrutura de Pastas

```
biblioteca-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/     ← os teus controllers
│   │   ├── Middleware/      ← middleware de roles
│   │   └── Requests/        ← validação de inputs
│   └── Models/              ← modelos Eloquent
├── database/
│   ├── migrations/          ← estrutura das tabelas
│   ├── seeders/             ← dados de teste
│   └── database.sqlite      ← ficheiro da BD
├── routes/
│   └── api.php              ← rotas da API
├── .env                     ← variáveis de ambiente
└── composer.json            ← dependências
```

> 📝 **NOTA:** Compara esta estrutura com o que usaste em Node.js. Em vez de models/, routes/, controllers/ separados na raiz, o Laravel organiza tudo dentro de `app/`. A lógica é a mesma — a organização é mais rígida e convencional.

## 1.6 Iniciar o Servidor

```bash
php artisan serve

# Output esperado:
# INFO  Server running on [http://127.0.0.1:8000].
```

> 🎯 **QUESTÕES DE COMPREENSÃO — Fase 1:**
> 1. Qual é a diferença entre DB_CONNECTION e DB_DATABASE no .env?
> 2. O que faz o comando `php artisan serve`? Qual o equivalente em Node.js?
> 3. Porque é que o SQLite não precisa de servidor? Que limitações tem?

---

# Fase 2 — Modelos e Migrações

O Eloquent é o ORM do Laravel. Cada modelo representa uma tabela na base de dados. As migrações definem a estrutura das tabelas e permitem versionar o esquema da base de dados.

## 2.1 Planear as Relações

```
User (utilizador)
  └── hasMany ──► Reserva (um utilizador pode ter muitas reservas)

Autor
  └── hasMany ──► Livro (um autor pode ter muitos livros)

Livro
  ├── belongsTo ──► Autor
  └── belongsToMany ──► Reserva (através de livro_reserva)

Reserva
  ├── belongsTo ──► User
  └── belongsToMany ──► Livro
```

## 2.2 Criar os Modelos e Migrações

O flag `-m` cria automaticamente a migração associada ao modelo:

```bash
php artisan make:model Autor -m
php artisan make:model Livro -m
php artisan make:model Reserva -m

# Criar a tabela pivot (relação N:N entre livros e reservas)
php artisan make:migration create_livro_reserva_table
```

## 2.3 Definir as Migrações

### Migração: autors

```php
// database/migrations/xxxx_create_autors_table.php
public function up(): void
{
    Schema::create('autors', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('nacionalidade')->nullable();
        $table->date('data_nascimento')->nullable();
        $table->text('biografia')->nullable();
        $table->timestamps();
    });
}
```

### Migração: livros

```php
// database/migrations/xxxx_create_livros_table.php
public function up(): void
{
    Schema::create('livros', function (Blueprint $table) {
        $table->id();
        $table->string('titulo');
        $table->string('isbn')->unique();
        $table->integer('ano_publicacao');
        $table->string('genero');
        $table->integer('exemplares_disponiveis')->default(1);
        $table->foreignId('autor_id')->constrained('autors')->onDelete('cascade');
        $table->timestamps();
    });
}
```

### Migração: reservas

```php
// database/migrations/xxxx_create_reservas_table.php
public function up(): void
{
    Schema::create('reservas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->date('data_reserva');
        $table->date('data_devolucao');
        $table->enum('estado', ['pendente', 'ativa', 'devolvida'])->default('pendente');
        $table->timestamps();
    });
}
```

### Migração: tabela pivot livro_reserva

```php
// database/migrations/xxxx_create_livro_reserva_table.php
public function up(): void
{
    Schema::create('livro_reserva', function (Blueprint $table) {
        $table->id();
        $table->foreignId('livro_id')->constrained()->onDelete('cascade');
        $table->foreignId('reserva_id')->constrained()->onDelete('cascade');
        $table->integer('quantidade')->default(1);
        $table->timestamps();
    });
}
```

## 2.4 Adicionar o Campo Role ao User

```bash
php artisan make:migration add_role_to_users_table --table=users
```

```php
// database/migrations/xxxx_add_role_to_users_table.php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'leitor'])->default('leitor');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
```

## 2.5 Correr as Migrações

```bash
php artisan migrate

# Para recomeçar do zero (apaga tudo e recria):
php artisan migrate:fresh
```

## 2.6 Definir os Modelos Eloquent

### Modelo: Autor

```php
// app/Models/Autor.php
class Autor extends Model
{
    protected $fillable = ['nome', 'nacionalidade', 'data_nascimento', 'biografia'];

    // Um autor tem muitos livros
    public function livros()
    {
        return $this->hasMany(Livro::class);
    }
}
```

### Modelo: Livro

```php
// app/Models/Livro.php
class Livro extends Model
{
    protected $fillable = ['titulo', 'isbn', 'ano_publicacao', 'genero',
                           'exemplares_disponiveis', 'autor_id'];

    // Um livro pertence a um autor
    public function autor()
    {
        return $this->belongsTo(Autor::class);
    }

    // Um livro pode estar em muitas reservas
    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'livro_reserva')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }
}
```

### Modelo: Reserva

```php
// app/Models/Reserva.php
class Reserva extends Model
{
    protected $fillable = ['user_id', 'data_reserva', 'data_devolucao', 'estado'];

    // Uma reserva pertence a um utilizador
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Uma reserva inclui vários livros
    public function livros()
    {
        return $this->belongsToMany(Livro::class, 'livro_reserva')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }
}
```

### Modelo: User (atualizar)

```php
// app/Models/User.php — adicionar ao modelo existente
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    // Um utilizador tem muitas reservas
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    // Método auxiliar para verificar o role
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
```

> 🎯 **QUESTÕES DE COMPREENSÃO — Fase 2:**
> 1. Qual é a diferença entre `hasMany` e `belongsTo`? Dá um exemplo com as entidades deste projeto.
> 2. O que é uma tabela pivot? Porque é necessária para a relação Livro-Reserva?
> 3. O que faz `$fillable`? Porque é importante definí-lo?

---

# Fase 3 — Autenticação com Sanctum

O Laravel Sanctum cria tokens de API para os utilizadores. O fluxo é idêntico ao JWT que usaste em Node: o cliente envia o token no header de cada pedido protegido.

## 3.1 Configurar as Rotas

```php
// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Rotas protegidas (requerem token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
});
```

## 3.2 Criar o AuthController

```bash
php artisan make:controller AuthController
```

```php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // POST /api/register
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'leitor',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registo efetuado com sucesso',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    // POST /api/login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login efetuado com sucesso',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    // POST /api/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout efetuado com sucesso']);
    }

    // GET /api/me
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
```

> 💡 **Comparação com Node.js + JWT:**
> - Node: `jwt.sign(payload, secret)` → Laravel: `$user->createToken('nome')->plainTextToken`
> - Node: `jwt.verify(token, secret)` → Laravel: middleware `auth:sanctum` (automático)
> - Node: `req.user` → Laravel: `$request->user()`

## 3.3 Testar no Postman — Autenticação

**Registo de utilizador:**
```
POST http://127.0.0.1:8000/api/register
Content-Type: application/json

{
  "name": "Ana Silva",
  "email": "ana@exemplo.pt",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Login (guarda o token retornado):**
```
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
  "email": "ana@exemplo.pt",
  "password": "password123"
}
```

**Pedido autenticado:**
```
GET http://127.0.0.1:8000/api/me
Authorization: Bearer SEU_TOKEN_AQUI
```

> 📝 **NOTA:** No Postman, podes guardar o token numa variável de ambiente. Na aba **Tests** do pedido de login, adiciona:
> ```js
> pm.environment.set('token', pm.response.json().token);
> ```
> Nos pedidos seguintes, usa `{{token}}` no header Authorization.

---

# Fase 4 — Roles e Middleware

## 4.1 Criar o Middleware de Role

```bash
php artisan make:middleware CheckRole
```

```php
// app/Http/Middleware/CheckRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (! $request->user() || $request->user()->role !== $role) {
            return response()->json([
                'message' => 'Acesso negado. Permissões insuficientes.'
            ], 403);
        }

        return $next($request);
    }
}
```

## 4.2 Registar o Middleware

No Laravel 11+, regista em `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
```

## 4.3 Usar o Middleware nas Rotas

```php
// routes/api.php

Route::middleware('auth:sanctum')->group(function () {

    // Rotas acessíveis a qualquer utilizador autenticado
    Route::get('/livros',        [LivroController::class, 'index']);
    Route::get('/livros/{id}',   [LivroController::class, 'show']);
    Route::get('/autores',       [AutorController::class, 'index']);

    // Rotas exclusivas do leitor
    Route::middleware('role:leitor')->group(function () {
        Route::get('/reservas/minhas',  [ReservaController::class, 'minhas']);
        Route::post('/reservas',        [ReservaController::class, 'store']);
    });

    // Rotas exclusivas do administrador
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('/autores', AutorController::class)
             ->except(['index', 'show']);
        Route::apiResource('/livros', LivroController::class)
             ->except(['index', 'show']);
        Route::get('/reservas',         [ReservaController::class, 'index']);
        Route::patch('/reservas/{id}',  [ReservaController::class, 'updateEstado']);
    });
});
```

> 🎯 **QUESTÕES DE COMPREENSÃO — Fases 3 e 4:**
> 1. O que é o Bearer Token e como funciona? Como se compara ao JWT?
> 2. O que acontece se tentares aceder a uma rota admin com um token de leitor?
> 3. Qual é a diferença entre autenticação e autorização?

---

# Fase 5 — CRUD e Validação

## 5.1 Criar os Controllers

```bash
php artisan make:controller AutorController --api
php artisan make:controller LivroController --api
php artisan make:controller ReservaController --api
```

> 💡 **DICA:** O flag `--api` cria automaticamente os métodos index, store, show, update e destroy.

## 5.2 AutorController — CRUD Completo

```php
// app/Http/Controllers/AutorController.php
class AutorController extends Controller
{
    // GET /api/autores
    public function index()
    {
        $autores = Autor::withCount('livros')->paginate(10);
        return response()->json($autores);
    }

    // POST /api/autores
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'            => 'required|string|max:255',
            'nacionalidade'   => 'nullable|string|max:100',
            'data_nascimento' => 'nullable|date',
            'biografia'       => 'nullable|string',
        ]);

        $autor = Autor::create($validated);
        return response()->json($autor, 201);
    }

    // GET /api/autores/{id}
    public function show(string $id)
    {
        $autor = Autor::with('livros')->findOrFail($id);
        return response()->json($autor);
    }

    // PUT/PATCH /api/autores/{id}
    public function update(Request $request, string $id)
    {
        $autor = Autor::findOrFail($id);

        $validated = $request->validate([
            'nome'            => 'sometimes|string|max:255',
            'nacionalidade'   => 'nullable|string|max:100',
            'data_nascimento' => 'nullable|date',
            'biografia'       => 'nullable|string',
        ]);

        $autor->update($validated);
        return response()->json($autor);
    }

    // DELETE /api/autores/{id}
    public function destroy(string $id)
    {
        $autor = Autor::findOrFail($id);
        $autor->delete();
        return response()->json(['message' => 'Autor eliminado com sucesso']);
    }
}
```

## 5.3 LivroController — com Filtros e Eager Loading

```php
// app/Http/Controllers/LivroController.php
class LivroController extends Controller
{
    // GET /api/livros?genero=romance&disponivel=1
    public function index(Request $request)
    {
        $query = Livro::with('autor');

        if ($request->has('genero')) {
            $query->where('genero', $request->genero);
        }

        if ($request->has('disponivel')) {
            $query->where('exemplares_disponiveis', '>', 0);
        }

        return response()->json($query->paginate(10));
    }

    // POST /api/livros
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'                  => 'required|string|max:255',
            'isbn'                    => 'required|string|unique:livros',
            'ano_publicacao'          => 'required|integer|min:1000|max:2100',
            'genero'                  => 'required|string|max:100',
            'exemplares_disponiveis'  => 'required|integer|min:0',
            'autor_id'                => 'required|exists:autors,id',
        ]);

        $livro = Livro::create($validated);
        return response()->json($livro->load('autor'), 201);
    }

    // GET /api/livros/{id}
    public function show(string $id)
    {
        $livro = Livro::with('autor')->findOrFail($id);
        return response()->json($livro);
    }

    // PUT /api/livros/{id}
    public function update(Request $request, string $id)
    {
        $livro = Livro::findOrFail($id);

        $validated = $request->validate([
            'titulo'                  => 'sometimes|string|max:255',
            'isbn'                    => 'sometimes|string|unique:livros,isbn,'.$id,
            'ano_publicacao'          => 'sometimes|integer|min:1000|max:2100',
            'genero'                  => 'sometimes|string|max:100',
            'exemplares_disponiveis'  => 'sometimes|integer|min:0',
            'autor_id'                => 'sometimes|exists:autors,id',
        ]);

        $livro->update($validated);
        return response()->json($livro->load('autor'));
    }

    // DELETE /api/livros/{id}
    public function destroy(string $id)
    {
        $livro = Livro::findOrFail($id);
        $livro->delete();
        return response()->json(['message' => 'Livro eliminado com sucesso']);
    }
}
```

## 5.4 ReservaController

```php
// app/Http/Controllers/ReservaController.php
class ReservaController extends Controller
{
    // GET /api/reservas — admin vê todas
    public function index()
    {
        $reservas = Reserva::with(['user', 'livros'])->paginate(15);
        return response()->json($reservas);
    }

    // GET /api/reservas/minhas — leitor vê as suas
    public function minhas(Request $request)
    {
        $reservas = $request->user()->reservas()->with('livros')->get();
        return response()->json($reservas);
    }

    // POST /api/reservas
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_reserva'            => 'required|date|after_or_equal:today',
            'data_devolucao'          => 'required|date|after:data_reserva',
            'livros'                  => 'required|array|min:1',
            'livros.*.id'             => 'required|exists:livros,id',
            'livros.*.quantidade'     => 'required|integer|min:1',
        ]);

        $reserva = Reserva::create([
            'user_id'        => $request->user()->id,
            'data_reserva'   => $validated['data_reserva'],
            'data_devolucao' => $validated['data_devolucao'],
            'estado'         => 'pendente',
        ]);

        // Associar livros à reserva (tabela pivot)
        foreach ($validated['livros'] as $livro) {
            $reserva->livros()->attach($livro['id'], ['quantidade' => $livro['quantidade']]);
        }

        return response()->json($reserva->load('livros'), 201);
    }

    // PATCH /api/reservas/{id} — admin atualiza estado
    public function updateEstado(Request $request, string $id)
    {
        $reserva = Reserva::findOrFail($id);

        $request->validate([
            'estado' => 'required|in:pendente,ativa,devolvida',
        ]);

        $reserva->update(['estado' => $request->estado]);
        return response()->json($reserva);
    }
}
```

> 📝 **NOTA:** O método `attach()` insere registos na tabela pivot `livro_reserva`. O `with('livros')` / `with('autor')` chama-se **Eager Loading** — carrega as relações numa só query SQL em vez de N queries separadas.

---

# Fase 6 — Seeders e Dados de Teste

## 6.1 Criar os Seeders

```bash
php artisan make:seeder UserSeeder
php artisan make:seeder AutorSeeder
php artisan make:seeder LivroSeeder
```

```php
// database/seeders/UserSeeder.php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@biblioteca.pt',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'João Leitor',
            'email'    => 'joao@exemplo.pt',
            'password' => Hash::make('leitor123'),
            'role'     => 'leitor',
        ]);
    }
}
```

```php
// database/seeders/AutorSeeder.php
use App\Models\Autor;

class AutorSeeder extends Seeder
{
    public function run(): void
    {
        $autores = [
            ['nome' => 'José Saramago',   'nacionalidade' => 'Portuguesa', 'data_nascimento' => '1922-11-16'],
            ['nome' => 'Fernando Pessoa', 'nacionalidade' => 'Portuguesa', 'data_nascimento' => '1888-06-13'],
            ['nome' => 'Eça de Queirós', 'nacionalidade' => 'Portuguesa', 'data_nascimento' => '1845-11-25'],
        ];

        foreach ($autores as $autor) {
            Autor::create($autor);
        }
    }
}
```

```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    $this->call([
        UserSeeder::class,
        AutorSeeder::class,
        LivroSeeder::class,
    ]);
}
```

```bash
# Correr os seeders
php artisan db:seed

# Recriar a BD e popular (muito útil durante o desenvolvimento)
php artisan migrate:fresh --seed
```

---

# Fase 7 — Referência de Endpoints

## 7.1 Autenticação

| Método | Endpoint | Autenticação | Descrição |
|---|---|---|---|
| POST | /api/register | Pública | Registo de novo utilizador |
| POST | /api/login | Pública | Login — retorna token |
| POST | /api/logout | Token | Invalida o token atual |
| GET | /api/me | Token | Perfil do utilizador autenticado |

## 7.2 Autores

| Método | Endpoint | Autenticação | Descrição |
|---|---|---|---|
| GET | /api/autores | Token | Listar todos os autores (paginado) |
| GET | /api/autores/{id} | Token | Ver autor e os seus livros |
| POST | /api/autores | Admin | Criar novo autor |
| PUT | /api/autores/{id} | Admin | Atualizar autor |
| DELETE | /api/autores/{id} | Admin | Eliminar autor |

## 7.3 Livros

| Método | Endpoint | Autenticação | Descrição |
|---|---|---|---|
| GET | /api/livros | Token | Listar livros (filtros: genero, disponivel) |
| GET | /api/livros/{id} | Token | Ver livro com autor |
| POST | /api/livros | Admin | Criar livro |
| PUT | /api/livros/{id} | Admin | Atualizar livro |
| DELETE | /api/livros/{id} | Admin | Eliminar livro |

## 7.4 Reservas

| Método | Endpoint | Autenticação | Descrição |
|---|---|---|---|
| GET | /api/reservas | Admin | Listar todas as reservas |
| GET | /api/reservas/minhas | Leitor | Ver as minhas reservas |
| POST | /api/reservas | Leitor | Criar nova reserva com livros |
| PATCH | /api/reservas/{id} | Admin | Atualizar estado da reserva |

## 7.5 Referência — Códigos HTTP

| Código | Nome | Quando usar |
|---|---|---|
| 200 | OK | Pedido bem-sucedido (GET, PUT, PATCH) |
| 201 | Created | Recurso criado com sucesso (POST) |
| 204 | No Content | Eliminação bem-sucedida (DELETE) |
| 400 | Bad Request | Pedido mal formado ou dados inválidos |
| 401 | Unauthorized | Token em falta ou inválido |
| 403 | Forbidden | Autenticado mas sem permissão (role errado) |
| 404 | Not Found | Recurso não encontrado |
| 422 | Unprocessable Entity | Falha de validação (campos inválidos) |
| 500 | Internal Server Error | Erro interno do servidor |

---

# Fase 8 — Testes com Postman

## 8.1 Configurar o Ambiente

1. Abre o Postman e cria um novo **Environment** chamado `Biblioteca API`
2. Adiciona as variáveis:
   - `base_url` → `http://127.0.0.1:8000/api`
   - `token` → (deixar vazio — será preenchido automaticamente)
3. Em cada pedido, usa `{{base_url}}/autores` em vez do URL completo

## 8.2 Guardar o Token Automaticamente

No pedido de login, vai à aba **Tests** e adiciona:

```javascript
if (pm.response.code === 200) {
    const json = pm.response.json();
    pm.environment.set('token', json.token);
    console.log('Token guardado:', json.token);
}
```

Nos pedidos protegidos, configura o header:

```
Authorization: Bearer {{token}}
Content-Type: application/json
Accept: application/json
```

## 8.3 Exemplos de Pedidos

**Criar um Autor (Admin):**
```json
POST {{base_url}}/autores
Authorization: Bearer {{token}}

{
  "nome": "Valter Hugo Mãe",
  "nacionalidade": "Portuguesa",
  "data_nascimento": "1971-04-02",
  "biografia": "Escritor e poeta português."
}
```

**Criar um Livro (Admin):**
```json
POST {{base_url}}/livros
Authorization: Bearer {{token}}

{
  "titulo": "O Apocalipse dos Trabalhadores",
  "isbn": "978-972-23-3845-1",
  "ano_publicacao": 2008,
  "genero": "Romance",
  "exemplares_disponiveis": 3,
  "autor_id": 1
}
```

**Criar uma Reserva (Leitor):**
```json
POST {{base_url}}/reservas
Authorization: Bearer {{token}}

{
  "data_reserva": "2025-02-01",
  "data_devolucao": "2025-02-15",
  "livros": [
    { "id": 1, "quantidade": 1 },
    { "id": 2, "quantidade": 1 }
  ]
}
```

**Listar Livros com Filtro:**
```
GET {{base_url}}/livros?genero=Romance&disponivel=1
Authorization: Bearer {{token}}
```

## 8.4 Checklist de Testes

- [ ] Registo com dados válidos → 201
- [ ] Registo com email duplicado → 422
- [ ] Login com credenciais corretas → 200 + token
- [ ] Login com password errada → 422
- [ ] GET /livros sem token → 401
- [ ] POST /autores com token de leitor → 403
- [ ] POST /autores com token de admin → 201
- [ ] GET /livros/{id} com id inexistente → 404
- [ ] POST /reservas com data_devolucao antes de data_reserva → 422
- [ ] PATCH /reservas/{id} — alterar estado para 'devolvida' → 200

---

# Critérios de Avaliação

| Critério | Peso | Descrição |
|---|---|---|
| Configuração e Estrutura | 15% | Projeto Laravel funcional, SQLite configurado, .env.example incluído |
| Modelos e Migrações | 20% | Modelos corretos com relações definidas, migrações completas |
| Autenticação (Sanctum) | 15% | Registo, login e logout funcionais, token gerado e validado |
| Roles e Middleware | 10% | Middleware de role implementado, rotas protegidas corretamente |
| CRUD e Validação | 20% | Todos os endpoints implementados, validação em todos os campos |
| Seeders | 5% | Dados de teste relevantes, pelo menos 1 admin e 2 leitores |
| Testes Postman | 10% | Coleção exportada com todos os endpoints e cenários de erro |
| Relatório de Arquitetura | 5% | Documento descrevendo decisões técnicas, relações e endpoints |

## Entregáveis Obrigatórios

- [ ] Repositório Git (GitHub/GitLab) com histórico de commits
- [ ] Ficheiro `.env.example` (sem dados sensíveis)
- [ ] Coleção Postman exportada (`.json`) incluída no repositório
- [ ] Seeders funcionais (`php artisan migrate:fresh --seed` deve funcionar)
- [ ] Relatório de arquitetura (PDF ou Markdown, máx. 2 páginas)

> ⚠️ **AVISO:** Não incluas o ficheiro `database.sqlite` no repositório Git! Adiciona-o ao `.gitignore`. Os seeders devem ser suficientes para recriar os dados.

> 🎯 **QUESTÕES FINAIS DE REFLEXÃO:**
> 1. Que vantagens e desvantagens identificas em usar Laravel vs Node.js/Express para esta API?
> 2. O que mudarias na arquitetura se o projeto fosse para produção com muitos utilizadores?
> 3. Como implementarias rate limiting para proteger a API de abusos?

---

*Bom trabalho! 🚀*

*Qualquer dúvida, coloca questões nas aulas ou no fórum da disciplina.*
