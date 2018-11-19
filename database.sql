/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.1.32-MariaDB : Database - db_api_slim_modelo
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_api_slim_modelo` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `db_api_slim_modelo`;

/*Table structure for table `sis_acl_groups` */

DROP TABLE IF EXISTS `sis_acl_groups`;

CREATE TABLE `sis_acl_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(10) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE_group` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `sis_acl_groups` */

insert  into `sis_acl_groups`(`id`,`group`,`description`) values (1,'admin','Grupo de permissões para usuários administradores.'),(2,'oreia','Grupo de permissões para usuários oeia.');

/*Table structure for table `sis_acl_groups_on_routes` */

DROP TABLE IF EXISTS `sis_acl_groups_on_routes`;

CREATE TABLE `sis_acl_groups_on_routes` (
  `group_id` int(10) unsigned NOT NULL,
  `route_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`route_id`),
  KEY `route_id` (`route_id`),
  CONSTRAINT `sis_acl_groups_on_routes_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `sis_acl_groups` (`id`),
  CONSTRAINT `sis_acl_groups_on_routes_ibfk_4` FOREIGN KEY (`route_id`) REFERENCES `sis_acl_routes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sis_acl_groups_on_routes` */

insert  into `sis_acl_groups_on_routes`(`group_id`,`route_id`) values (1,1),(1,2),(1,3),(1,4),(1,5),(2,1),(2,2);

/*Table structure for table `sis_acl_routes` */

DROP TABLE IF EXISTS `sis_acl_routes`;

CREATE TABLE `sis_acl_routes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route` varchar(20) NOT NULL,
  `method` varchar(50) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `route` (`route`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `sis_acl_routes` */

insert  into `sis_acl_routes`(`id`,`route`,`method`,`description`) values (1,'read_all_news','GET','Permite listar todos os registros da tabela news.'),(2,'read_one_news','GET','Permite consultar um registro expecífico da tabela news.'),(3,'write_news','POST','Permite criar registros na tabela news.'),(4,'update_news','PUT','Permite atualizar registros na tabela news.'),(5,'delete_news','DELETE','Permite apagar registros na tabela news.');

/*Table structure for table `sis_acl_users` */

DROP TABLE IF EXISTS `sis_acl_users`;

CREATE TABLE `sis_acl_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL,
  `senha` varchar(20) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE_login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `sis_acl_users` */

insert  into `sis_acl_users`(`id`,`login`,`senha`,`nome`,`email`,`criado_em`) values (1,'thiago23','tac23','Thiago Cabral','thiago231286@gmail.com','2018-11-12 13:19:04'),(2,'angela12','abc12','Angela Barbosa','angeça_odonto@yahoo.com.br','2018-11-12 13:19:01'),(3,'alice16','abc16','Alice Cabral','alice2018@gmail.com','2018-11-12 14:10:32');

/*Table structure for table `sis_acl_users_on_groups` */

DROP TABLE IF EXISTS `sis_acl_users_on_groups`;

CREATE TABLE `sis_acl_users_on_groups` (
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `sis_acl_users_on_groups_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `sis_acl_users` (`id`),
  CONSTRAINT `sis_acl_users_on_groups_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `sis_acl_groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sis_acl_users_on_groups` */

insert  into `sis_acl_users_on_groups`(`user_id`,`group_id`) values (1,1),(2,2),(3,1),(3,2);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
