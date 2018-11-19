<?php
// Application middleware

# Middleware de Autorização (ACL's)
$app->add(function($request, $response, $next) use ($app) {

    $acl = new App\Acl;
    $acl->createRoles();
    $acl->createResources();
    $acl->createPermissions();

    # retorna a rota que o cliente solicitou "rota atual"
    $route = $request->getAttribute('route');

    # Deixar seguir o fluxo em rotas que não exista
    if( !$route ){return $next($request, $response);}

    /* Libera rotas que não possuem 'setName'
     * Se não for liberado, todas as rotas devem ser registradas no Banco de Dados,
     * ter setado o setName(), inclusive a rota de de autenticação "/api/auth", que é a rota
     * liberada no JwtAuthentication autenticar os clientes e gerar Token's */
    if( !$route->getName() ){return $next($request, $response);}

    # Pego o Token completo Descriptografado
    $token_decrypted = $request->getAttribute('decoded_token_data');

    $role = $token_decrypted->data->userLogin; // login do usuário. Esse campo foi usado para criar as Roles da ACL's
    $resource = $route->getName(); // setName da rota
    $method = $route->getMethods()[0]; // pega o metodo da solicitação da rota 'get, post, put, ...'

    /* Chama o método da classe ACL passando, login do usuário, nome da rota e método.
     * A função verifica se o cliente tem acesso a rota e ao método e retorna um TRUE */
    $isAllowed = $acl->check($role, $resource, $method);

    # Tratamento do Retorno da $acl->check() ----    ----    ----    ----    ----    ----    ----    ----
    
    if(!$isAllowed) { // Executado quando o cliente NÃO TEM PERMISSÃO para acessar a Rota/Método
        # Posso redirecioná-lo para uma rota expecífica
        #$url = $app->getContainer()->get('router')->pathFor('rota-deny'); // pego a rota atravez do setName()
        #return $response->withRedirect($url, 302); // redireciono o cliente com status codigo 302

        # Posso também retornar um JSON informando que o acesso foi negado
        return $response->withJson(['ACL' => 'DENY']);
    }

    # Se $isAllowed é TRUE, cliente tem permissão para acessar a Rota/Método. Continuar o Fluxo
    return $next($request, $response);
});



# Middleware de Autenticação (JWT Token)
$app->add(new Slim\Middleware\JwtAuthentication([
    "path" => ["/api"],
    "passthrough" => ["/api/auth"],
    "attribute" => "decoded_token_data", //quando o token é decodificado com êxito, o conteúdo do token decodificado é salvo aqui
    "secure" => true, //Se o middleware detectar um uso inseguro por HTTP, isso ocorrerá RuntimeException
    "relaxed" => ["localhost"], //Lista de host de desenvolvimento para ter uma segurança relaxada
    "secret" => base64_decode( $app->getContainer()->get('settings')['jwt']['secret'] ), // pegando o segredo-codigo "secret" do token guardado em settings
    "algorithm" => ["HS256"],
    "error" => function ($request, $response, $arguments){
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    },
]));
