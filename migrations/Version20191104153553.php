<?php

namespace Mf\Users;

use Mf\Migrations\AbstractMigration;
use Mf\Migrations\MigrationInterface;

class Version20191104153553 extends AbstractMigration implements MigrationInterface
{
    public static $description = "Create users table";

    public function up($schema)
    {
        switch ($this->db_type){
            case "mysql":{
                    $this->addSql("CREATE TABLE `users` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `login` char(127) NOT NULL COMMENT 'логин, можно мыло',
                      `status` int(11) NOT NULL COMMENT 'состояние юзера',
                      `password` char(127) DEFAULT NULL COMMENT 'текущий пароль',
                      `name` char(127) DEFAULT NULL COMMENT 'псевдоним',
                      `full_name` char(255) DEFAULT NULL COMMENT 'ФИО',
                      `temp_password` char(127) DEFAULT NULL COMMENT 'временный пароль для восстановления',
                      `temp_date` datetime DEFAULT NULL COMMENT 'дата годности временного пароля для активации',
                      `confirm_hash` char(50) DEFAULT NULL COMMENT 'строка для подтверждения регистрации',
                      `date_registration` datetime DEFAULT NULL COMMENT 'дата регистрации',
                      `date_last_login` datetime DEFAULT NULL COMMENT 'дата входа',
                      PRIMARY KEY (`id`),
                      KEY `temp_date` (`temp_date`),
                      KEY `confirm_hash` (`confirm_hash`),
                      KEY `status` (`status`),
                      KEY `date_registration` (`date_registration`),
                      KEY `login` (`login`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='регистрированные юзеры (база)'");

                    $this->addSql("INSERT INTO `users` VALUES (1,'root',3,'$2y$10$R4b7CZhWutZPDlNFoUF9Se/LVeHrWtsnRj4OM4HxC4yxc3scLObYW','root','root',NULL,NULL,NULL,NULL,NULL)");
                    $this->addSql("INSERT INTO `users` VALUES (2,'guest',4,'$2y$10$R4b7CZhWutZPDlNFoUF9Se/LVeHrWtsnRj4OM4HxC4yxc3scLObYW','guest','guest',NULL,NULL,NULL,NULL,NULL)");
                    $this->addSql("INSERT INTO `users` VALUES (11,'test',4,'$2y$10$R4b7CZhWutZPDlNFoUF9Se/LVeHrWtsnRj4OM4HxC4yxc3scLObYW','guest','guest',NULL,NULL,NULL,NULL,NULL)");

                    $this->addSql("CREATE TABLE `users_group` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `name` char(127) DEFAULT NULL,
                      `description` char(255) DEFAULT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='Группы'");
                    $this->addSql("INSERT INTO `users_group` VALUES (1,'Администраторы','Администратор - полный доступ ко всем ресурсам'),(2,'Гости','Гостевой вход - группа с минимальным уровнем доступа'),(6,'Редакторы','Редакторы информации'),(7,'Регистрированные посетители','Регистрированные посетители');");


                    $this->addSql("CREATE TABLE `users2group` (
                      `users` int(11) NOT NULL,
                      `users_group` int(11) NOT NULL,
                      PRIMARY KEY (`users`,`users_group`),
                      KEY `users` (`users`),
                      KEY `users_group` (`users_group`),
                      CONSTRAINT `users2group_fk` FOREIGN KEY (`users`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                      CONSTRAINT `users2group_fk1` FOREIGN KEY (`users_group`) REFERENCES `users_group` (`id`) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='привязка юзер-группа'");
                    $this->addSql("INSERT INTO `users2group` VALUES (1,1)");
                    $this->addSql("INSERT INTO `users2group` VALUES (2,2)");
                    $this->addSql("INSERT INTO `users2group` VALUES (11,2)");

                    $this->addSql("CREATE TABLE `users_ext` (
                      `id` int(11) NOT NULL,
                      PRIMARY KEY (`id`),
                      CONSTRAINT `users_ext_fk` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE
                    ) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COMMENT='расширение юзеров'");
                    $this->addSql("INSERT INTO `users_ext` VALUES (1)");
                    $this->addSql("INSERT INTO `users_ext` VALUES (2)");
                    $this->addSql("INSERT INTO `users_ext` VALUES (11)");



                    $this->addSql("CREATE TABLE `users_group_tree` (
                      `id` int(11) NOT NULL,
                      `parent_id` int(11) NOT NULL,
                      PRIMARY KEY (`id`,`parent_id`),
                      KEY `role` (`id`),
                      KEY `parent_role` (`parent_id`),
                      CONSTRAINT `users_group_tree_fk` FOREIGN KEY (`id`) REFERENCES `users_group` (`id`) ON DELETE CASCADE,
                      CONSTRAINT `users_group_tree_fk1` FOREIGN KEY (`parent_id`) REFERENCES `users_group` (`id`) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

                break;
            }
            default:{
                throw new \Exception("the database {$this->db_type} is not supported !");
            }
        }        
    }

    public function down($schema)
    {
        switch ($this->db_type){
            case "mysql":{
                $this->addSql("drop table `users_group_tree`");
                $this->addSql("drop table `users2group`");
                $this->addSql("drop table `users_group`");
                $this->addSql("drop table `users_ext`");
                $this->addSql("drop table `users`");
                break;
            }
            default:{
                throw new \Exception("the database {$this->db_type} is not supported !");
            }
        }
    }
}
