<?php

namespace App;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use PDO;

class Acl {

    private $acl;
    private $pdo;

    public function __construct() {
        $this->acl = new ZendAcl;

        $this->pdo = new PDO("mysql:host=localhost;dbname=db_api_slim_modelo;charset=UTF8",
        'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    # Grupos de Usuários
    public function createRoles() {

        $sth = $this->pdo->prepare("SELECT login FROM sis_acl_users");
        $sth->execute();
        $users = $sth->fetchAll();

        foreach($users as $u){
            $this->acl->addRole( new Role($u['login']) );
        }
        
        /*
        $this->acl->addRole(new Role('guest'));
        $this->acl->addRole(new Role('member'), ['guest']); //grupo member também recebe as permissões do guest
        $this->acl->addRole(new Role('admin'));

        Cadastro de Usuários;
        Cadastro de Grupos:
        Cadastro de Rotas;
        Relação Usuario/Grupo (sis_acl_users_in_groups): usuario_id, grupo_id
        Cadastro de permissões: grupo_id, rota_id

        Na createRoles (grupos):
        Pegar todos os logins de usuários 

        Na createResources:
        Pegar e adicionar as Rotas

        Na createPermissions:
        Pegar todos os usuarios e suas rotas "eliminando linhas duplicadas DISTINC", 
        porque a mesma permissão pode está liberada em outro grupo, já que o usuário pode participar de 
        varios grupos
        */
    }

    # As rotas
    public function createResources() {

        $sth = $this->pdo->prepare("SELECT route, method FROM sis_acl_routes");
        $sth->execute();
        $routes = $sth->fetchAll();

        foreach($routes as $r){
            $this->acl->addResource(new Resource( $r['route'] ));
        }
        /*
        $this->acl->addResource(new Resource('rota-home'));
        $this->acl->addResource(new Resource('rota-sobre'));
        $this->acl->addResource(new Resource('rota-contato'));
        $this->acl->addResource(new Resource('rota-deny'));*/
    }

    public function createPermissions() {

        $sth = $this->pdo->prepare("
            SELECT DISTINCT
                u.login as login,
                r.route as route,
                r.method as method
            FROM sis_acl_users_on_groups ug
            INNER JOIN sis_acl_users u ON u.id = ug.user_id
            INNER JOIN sis_acl_groups_on_routes gr ON gr.group_id = ug.group_id
            INNER JOIN sis_acl_routes r ON r.id = gr.route_id
            ORDER BY u.id ASC
        ");
        $sth->execute();
        $permissions = $sth->fetchAll();

        foreach($permissions as $p){
            $this->acl->allow($p['login'], $p['route'], $p['method']);
        }

        /*
        # O grupo de usuário 'guest' poderá acessar a rota 'home' atravez do protocolo 'get' 
        $this->acl->allow('guest', 'rota-home', 'GET');
        $this->acl->allow('guest', 'rota-deny', ['GET', 'POST']);

        $this->acl->allow('member', 'rota-sobre', ['GET', 'POST']);

        $this->acl->allow('admin'); // grupo 'admin' pode acessar todas as rotas*/
    }

    public function check($role, $resource, $method) {
        return $this->acl->isAllowed($role, $resource, $method);
    }

    
}