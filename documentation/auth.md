# Autenticação

## Inclusão de usuários da Empresa

Os usuários são criados conforme quadro GIP (sistema de RH) e são atualizados por processo de carga.

```
Comando: php artisan user:update
Local: App\Console\Commands\Core\UserCommand.php
Rotina: Todos os dias às 01:00
```

## Inclusão de usuários outros

Usuários externos (matrícula com prefixo `ext`) permitidos no diretório LDAP do cliente são criados automaticamente no primeiro acesso, validados pelo diretório, e seu tipo é definido com as letras da matrícula.

```
Função: App\Services\Core\Auth\AuthService::createLdapUser($username, $name)
```

## Login

O login pode ser feito de 3 formas, e todas as chamadas são feitas na rota `/api/auth/login`, controlador `App\Http\Controllers\Core\AuthController`.

1. Local:
    - **Descrição:** Disponível apenas em ambiente de desenvolvimento, com a senha padrão 123456. Em produção, essa senha é um hash aleatório.
    - **Função:** `App\Services\Core\Auth\AuthService::authLocal()`
2. IdP corporativo (matrículas `usr`):
    - **Descrição:** Autentica no IdP corporativo da Empresa (`CORPORATE_IDP_URL`) com matrícula e senha corporativas.
    - **Função:** `App\Services\Core\Auth\AuthService::authCorporateIdp()`
3. Diretório LDAP (matrículas `ext`):
    - **Descrição:** Autentica no diretório LDAP do cliente (`LDAP_DIRECTORY_URL`).
    - **Função:** `App\Services\Core\Auth\AuthService::authLdap()`

## Autorizações

As permissões são definidas por rotas e podem ser cadastradas na tabela `core.route_permissions`. Ao acessar o Ahtlas, os dados do usuário são comparados com as regras da tabela `core.route_permissions` para definir quais rotas são liberadas para ele. No sistema, o responsável por gerenciar esse processo é o middleware `App\Http\Middleware\RoutePermission.php`.

### Campos para comparação e definição de regras

| Campo              | Tipo           | Exemplo                                            |
|--------------------|----------------|----------------------------------------------------|
| route              | string         | /api/administration/intelligence/indicators/update |
| username           | array[string]  | ["usr000001"]                                      |
| sector_n1_id       | array[integer] | [100,200]                                          |
| manager_n1_id      | array[string]  | ["usr000002"]                                      |
| manager_n2_id      | array[string]  | ["usr000002"]                                      |
| manager_n3_id      | array[string]  | ["usr000002"]                                      |
| manager_n4_id      | array[string]  | ["usr000002"]                                      |
| manager_n5_id      | array[string]  | ["usr000002"]                                      |
| hierarchical_level | array[integer] | [3,4,5] de 0 a 5 (0 = agentes e não gestores)      |
| staff              | boolean / null | null                                               |
| type               | array[string]  | ["usr"]                                            |
| uf                 | array[string]  | ["go"]                                             |
| position_summary   | array[string]  | ["agente"]                                         |

## Observações

1. As regras criadas por registro são tratadas em conjunto e podem se anular. Exemplo: Se liberar uma rota por username e por setor, mas o username não existir no setor, essa regra não liberará acesso para ninguém.

2. Podem ser criados vários registros para uma única rota com a finalidade de liberações diversas. Exemplo: um registro para liberar a rota para todos de um determinado setor (`sector_n1_id: [2672]`) e outro registro para liberar exclusivamente para um usuário (`username: ["usr000001"]`).

3. Usuários **admin** não consideram essas regras e têm acesso irrestrito. Para liberar o acesso admin, basta registrar a matrícula na tabela `core.user_admins`.