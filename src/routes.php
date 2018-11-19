<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;


$app->group('/api', function() {

    $this->get('/news',function(Request $request, Response $response, array $args) {
        
        # Variavel de retorno
        $result = Array();

        # Pega a conexão PDO da API
        $pdo = $this->db;

        try {
            $pdo->beginTransaction(); 
            $sql = "SELECT * FROM news ORDER BY id ASC";
            $stmt = $pdo->query($sql);
            $result['data']['total_rows'] = $stmt->rowCount();
            $result['data']['news'] = $stmt->fetchAll();

            $pdo->commit();
        } catch( PDOException $Exception ) {
            $pdo->rollBack();
            return $this->response->withJson([
                'error' => true,
                'code' => $Exception->getCode(),
                'message' => $Exception->getMessage()
            ]);
        }

        return $this->response->withJson($result);
        
    })->setName('read_all_news');
    
    $this->get('/news/{id}',function(Request $request, Response $response, array $args) {

        # get parametros
        $id_news = (int) $args['id'];

        # Variavel de retorno
        $result = Array();

        # Pega a conexão PDO da API
        $pdo = $this->db;
        
        try {
            # SQL de UPDATE
            $sql_news = "SELECT * FROM news WHERE id=:id_news";
            $sql_user = "SELECT id, nome FROM sis_acl_users WHERE id=:id_user";

            $pdo->beginTransaction();

            # Buscando os dados da NEWS
            $stmt = $pdo->prepare($sql_news);
            $stmt->execute( ["id_news" => $id_news] );
            $result['data']['news'] = $stmt->fetch();

            # Buscando os Dados do autor "USUARIO"
            $stmt = $pdo->prepare($sql_user);
            $stmt->execute( ["id_user" => $result['data']['news']['created_by'] ]);
            $result['data']['news']['created_by'] = $stmt->fetch();

            $pdo->commit();
        } catch( PDOException $Exception ) {
            $pdo->rollBack();
            return $this->response->withJson([
                'error' => true,
                'code' => $Exception->getCode(),
                'message' => $Exception->getMessage()
            ]);
        }

        return $this->response->withJson($result);

    })->setName('read_one_news');
    
    $this->post('/news',function(Request $request, Response $response, array $args) {

        # Variavel de retorno
        $result = Array();

        # Pega a conexão PDO da API
        $pdo = $this->db;

        # Pego o Token completo Descriptografado
        $token_decrypted = $request->getAttribute('decoded_token_data');
        
        $data = $request->getParams();
        $data['created_by'] = (int) $token_decrypted->data->userId;

        try {
            $pdo->beginTransaction(); 
            $sql = "INSERT INTO news(title, description, body, created_by) VALUES (:title, :description, :body, :created_by)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(  $data );
            $result['lastInsertId'] = $pdo->lastInsertId();
            $pdo->commit();
        } catch( PDOException $Exception ) {
            $pdo->rollBack();
            return $this->response->withJson([
                'error' => true,
                'code' => $Exception->getCode(),
                'message' => $Exception->getMessage()
            ]);
        }

        return $this->response->withJson([
            'rota' => Array(
                'name' => 'write_news',
                'method' => 'POST',
            ),
            
            "data" => $result
        ]);

    })->setName('write_news');
    
    $this->put('/news',function(Request $request, Response $response, array $args) {

        # Pega os Parametros Recebidos
        $data = $request->getParams();

        # Variavel de retorno
        $result = Array();

        # Pega a conexão PDO da API
        $pdo = $this->db;
        
        try {
            # SQL de UPDATE
            $sql = "UPDATE news SET title=:title, description=:description, body=:body, update_at=NOW(), delete_at=NULL WHERE id=:id";
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $result['success'] = $stmt->execute(  $data );
            $result['data']['row_affected'] = $stmt->rowCount();
            $pdo->commit();
        } catch( PDOException $Exception ) {
            $pdo->rollBack();
            return $this->response->withJson([
                'error' => true,
                'code' => $Exception->getCode(),
                'message' => $Exception->getMessage()
            ]);
        }

        return $this->response->withJson($result);

    })->setName('update_news');
    
    $this->delete('/news/{id}',function(Request $request, Response $response, array $args) {
       
        # get parametros
        $id = (int) $args['id'];

        # Variavel de retorno
        $result = Array();

        # Pega a conexão PDO da API
        $pdo = $this->db;

        try {
            $pdo->beginTransaction(); 
            $sql = "UPDATE news SET delete_at = NOW() WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $result['success'] = $stmt->execute(  ["id" => $id] );
            $result['data']['row_affected'] = $stmt->rowCount();
            $pdo->commit();
        } catch( PDOException $Exception ) {
            $pdo->rollBack();
            return $this->response->withJson([
                'error' => true,
                'code' => $Exception->getCode(),
                'message' => $Exception->getMessage()
            ]);
        }

        return $this->response->withJson($result);

    })->setName('delete_news');

    /* Rota Desprotegida (Não requer Token).
     * Necessário envio de Login e Senha de Usuário.
     * Se o Login e Senha estiverem corretos, é retornado um Token Válido. */
    $this->post('/auth', function (Request $request, Response $response, array $args) {
        
        // pega parametros recebidos
        $input = $request->getParsedBody();

        // consultando se o usuário existe no banco de dados. 
        try {
            $sql = "SELECT * FROM sis_acl_users WHERE login=:login";
            $sth = $this->db->prepare($sql);
            $sth->bindParam("login", $input['login']);
            $sth->execute();
            $user = $sth->fetchObject();
        } catch( PDOException $Exception ) {
            return $this->response->withJson([
                'error' => true,
                'code' => $Exception->getCode(),
                'message' => $Exception->getMessage()
            ]);
        }

        // verifica se o usuário existe.
        if(!$user) {
            // Caso o cliente não exista
            return $this->response->withJson(['error' => true, 'message' => 'Usuário não encontrdo.']);  
        }
     
        // Se o Cliente existir:
        // Comparo a senha informada com a senha cadastrada na Base de Dados. comparar a SENHA fornecida com um Hash usar if (!password_verify($input['senha'], $user->senha)) {
        if ( $input['senha'] != $user->senha ) {
            return $this->response->withJson(['error' => true, 'message' => 'Senha incorreta.']);  
        }

        /* Se usuário existe e senha foi informada corretamente, gero Token e retorno o mesmo
         * para que o cliente passe a informa-lo nos cabeçalhos das próximas requisições */

        // Gerando o Token
        $settings = $this->get('settings'); // 

        $tokenId    = base64_encode(mcrypt_create_iv(32));
        $issuedAt   = time();
        $notBefore  = $issuedAt + 10;       //adiciona 10 segundos
        $expire     = $notBefore + 86400;   // adiciona 86400 segundos (1 dia)

        $data = [
            'iat'  => $issuedAt,         // Hora em que a JWT foi emitido
            'jti'  => $tokenId,          // Identificador exclusivo para o JWT
            'nbf'  => $notBefore,        // Tempo o qual o JWT PASSA ser aceito
            'exp'  => $expire,           // Tempo de expiração o qual o JWT NÃO DEVE ser aceito
            'data' => [                  // Dados relacionados ao usuário autenticado
                'userId'    => $user->id,
                'userName'  => $user->nome,
                'userLogin' => $user->login,
                'userEmail' => $user->email
            ]
        ];
        
        $token = JWT::encode(
            $data,
            base64_decode( $settings['jwt']['secret'] ),
            "HS256"
        );

        return $this->response->withJson(['token' => $token]);
    });

    # Rota Protegida. Necessário envio de Token Válido
    $this->get('/users',function(Request $request, Response $response, array $args) {

        // descriptando o TOKEN do usuário Autenticado
        $result['token_decrypted'] = $request->getAttribute('decoded_token_data');

        // consultando tabela na base de dados
        $sth = $this->db->prepare("SELECT * FROM sis_acl_users ORDER BY id ASC");
        $sth->execute();
        $result['tb_users'] = $sth->fetchAll();

        return $this->response->withJson($result);
    }); 
});
