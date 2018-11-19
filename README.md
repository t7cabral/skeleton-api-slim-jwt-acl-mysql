# skeleton-api-slim-jwt-acl-mysql
Esqueleto de API Slim Framework com tuupola/slim-jwt-auth (Autenticação), zendframework/zend-permissions-acl (Autorização), MySQL e exemplo CRUD.

### Preparando o Projeto

* Dependências:

Acesse o diretório e baixe as dependências do projeto.
```
cd skeleton-api-slim-jwt-acl-mysql
composer install
```

* Bando de Dados: 

Crie o Banco de Dados e Tabelas no MySQL com o uso do arquivo 'database.sql';
Informe no arquivo src/settings.php [Linhas 11, 12, 13 e 14], as configurações de acesso;


### Executando o Projeto

* Executando o Projeto
```
composer start
```

### Testando a API

1º - Importe a coleção postman.json no aplicativo Postman;

2º - Execute a primeira Request "1. /api/auth" informando no Body o login e senha do Usuário. A solicitação retornará um Token, exemplo:
```
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.XXX.XXX"
}
```

3º - Coloque esse Token dentro das HEADERS dentro do atributo Authorization mantendo a palavra Bearer <TOKEN>, exemplo:
```
Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.XXX.XXX
```

OBS: O Token expira em 24 horas.

### Autênticação e Autorização:

Para Autenticar, use o login e senha guardados na tabela sis_acl_users;

Permissões:

O usuário id=1 (login = thiago23), tem permissão em todas as URLs

O Usuário id=2 (login = angela12), apenas as requisições:

1. /api/auth

2. /news

3. /news/{id}


