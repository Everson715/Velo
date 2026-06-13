# Identity Driver Service (IAM)

Este é o Microsserviço de Identidade e Acesso (IAM) responsável pela gestão de usuários (passageiros e motoristas), autenticação, sessões e perfis na arquitetura Velo.

## 🛠 Tecnologias Utilizadas

- **Linguagem**: PHP 8.2+ (com tipagem estrita `declare(strict_types=1);`)
- **Framework**: Laravel 10/11
- **Autenticação**: Laravel Sanctum (Tokens via Bearer Authentication)
- **Banco de Dados**: PostgreSQL
- **ORM / Migrations**: Prisma ORM (para estrutura e migrations), Eloquent (para interação com a aplicação Laravel)

## 🏗 Arquitetura

O microsserviço foi desenhado para manter um baixo acoplamento e alta coesão, utilizando as ferramentas nativas do Laravel:

- **Models (`app/Models`)**: Modelos `User` e `Vehicle` configurados para suportar UUIDs gerados pelo Prisma e relacionamentos do Eloquent. O `User` utiliza Traits do Sanctum (`HasApiTokens`) e `SoftDeletes`.
- **FormRequests (`app/Http/Requests`)**: Validação estrita de entradas HTTP fora dos controladores, garantindo segurança (ex: `RegisterRequest`, `LoginRequest`).
- **Controllers (`app/Http/Controllers`)**: Lógica de entrada e saída HTTP padronizada em JSON.
- **Policies (`app/Policies`)**: Centralização das regras de autorização para proteção contra Privilege Escalation e IDOR. Utilizadas juntamente com a facade `Gate`.
- **Middlewares (`app/Http/Middleware`)**: Interceptação de requisições, incluindo o `AuditLoggerMiddleware` para gravação de logs de auditoria de operações críticas.

## 🔌 Endpoints da API

Abaixo estão as rotas disponíveis na aplicação, agrupadas por funcionalidade. O prefixo base para todas as rotas é `/api`.

### 1. Cadastro e Onboarding (Público)
- `POST /api/users/register` - Criação de novos perfis.
- `POST /api/users/verify-email` - Validação de conta via e-mail.
- `POST /api/users/resend-verification` - Reenvio de credenciais de validação.

### 2. Autenticação e Recuperação (Público)
- `POST /api/auth/login` - Autenticação e geração de token Sanctum.
- `POST /api/auth/forgot-password` - Fluxo de recuperação de senha.
- `PATCH /api/auth/reset-password` - Redefinição de senha.

### 3. Autorização e Integração (Requer Autenticação)
- `GET /api/auth/verify` - Validação síncrona do token para consumo interno entre microsserviços.
- `POST /api/auth/logout` - Revoga o token atual da sessão logada.

### 4. Gestão de Perfil (Self-Service) (Requer Autenticação)
- `GET /api/me` - Retorna os dados do usuário autenticado (incluindo `vehicles` se for Motorista).
- `PATCH /api/me` - Atualização parcial do perfil.
- `POST /api/me/avatar` - Upload de imagem de perfil.
- `PUT /api/me/password` - Alteração da senha da conta.
- `GET /api/me/sessions` - Listagem de todas as sessões ativas (dispositivos).
- `DELETE /api/me/sessions/{id}` - Revogação de acesso de uma sessão específica.
- `DELETE /api/me/account` - Exclusão de conta (Soft Delete nativo).

## 🚀 Como Executar

Este projeto foi projetado para rodar nativamente via Docker.

1. Suba os containers do serviço:
   ```bash
   docker compose up -d
   ```
2. Caso o Prisma possua migrations pendentes, aplique-as:
   ```bash
   npx prisma db push
   # ou
   npx prisma migrate dev
   ```
3. O servidor backend da API estará ouvindo na porta configurada pelo container Docker (geralmente porta 8001 para o serviço de identidade).

## 🧪 Testes Automatizados

A aplicação conta com uma robusta suíte de testes de integração (Feature Tests) construída utilizando o PHPUnit com integração nativa ao Laravel e Sanctum.

Para rodar os testes isolados e garantir o funcionamento das regras de negócio, utilize o comando dentro do container principal (o banco em memória local `:velo_identity_test` será utilizado automaticamente):

```bash
docker exec -it velo-php-identity php artisan test
```

A cobertura (100% Passing) engloba:
- **Cadastro**: Emissão correta de respostas de erro para campos nulos e e-mails duplicados, e assertividade de banco de dados para os UUIDs.
- **Autenticação**: Emissão e revogação do Token Sanctum para Logins e Logouts de sistema.
- **Gestão de Sessões**: Bloqueio ativo de tokens antigos no DB e Soft Delete (Ocultação).

## 🔒 Segurança

- As senhas são processadas pela fachada `Hash` do Laravel (Bcrypt/Argon) e possuem regras fortes (mínimo de 8 caracteres, maiúsculas, minúsculas, números e símbolos). Em produção, as senhas são validadas contra vazamentos (`uncompromised`).
- Requisições protegidas operam sob o middleware `auth:sanctum`, com tokens configurados para expirarem em 2 horas. Há também rotas de `/refresh` para a rotação de tokens.
- Proteção contra tentativas de Força Bruta através de Rate Limiting (ex: 5 tentativas por minuto no `/login`, 3 no `/register`).
- Ações críticas que modificam o estado (POST, PUT, PATCH, DELETE) são registradas no banco de dados (`AuditLog`), rastreando `user_id`, endereço IP e ação.
- A autorização foi reforçada utilizando `Laravel Policies` (ex: `UserPolicy`) e `Gates` nos controllers para evitar vulnerabilidades de IDOR e escalonamento indevido de privilégios.
- Dados apagados não são removidos do banco (`SoftDeletes`), sendo apenas ocultados por questões de compliance.
