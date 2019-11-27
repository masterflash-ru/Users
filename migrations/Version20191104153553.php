<?php

namespace Mf\Users;

use Mf\Migrations\AbstractMigration;
use Mf\Migrations\MigrationInterface;
use Zend\Db\Sql\Ddl;
use Zend\Db\Sql;


class Version20191104153553 extends AbstractMigration implements MigrationInterface
{
    public static $description = "Create users table";

    public function up($schema, $adapter)
    {
        $table = new Ddl\CreateTable("users");
        $table->addColumn(new Ddl\Column\Integer('id',false,null,["AUTO_INCREMENT"=>true]));
        $table->addColumn(new Ddl\Column\Char('login', 127,false,"",["COMMENT"=>"логин, можно мыло"]));
        $table->addColumn(new Ddl\Column\Integer('status',false,0,["COMMENT"=>"состояние юзера"]));
        $table->addColumn(new Ddl\Column\Char('password', 127,false,"",["COMMENT"=>"текущий пароль"]));
        $table->addColumn(new Ddl\Column\Char('name', 127,false,"",["COMMENT"=>"псевдоним"]));
        $table->addColumn(new Ddl\Column\Char('full_name', 255,false,"",["COMMENT"=>"ФИО"]));
        $table->addColumn(new Ddl\Column\Char('temp_password', 127,true,"",["COMMENT"=>"временный пароль для восстановления"]));
        $table->addColumn(new Ddl\Column\Datetime('temp_date', true,null,["COMMENT"=>"временный пароль для восстановления"]));
        $table->addColumn(new Ddl\Column\Char('confirm_hash', 127,true,"",["COMMENT"=>"строка для подтверждения регистрации"]));
        $table->addColumn(new Ddl\Column\Datetime('date_registration', true,null,["COMMENT"=>"дата регистрации"]));
        $table->addColumn(new Ddl\Column\Datetime('date_last_login', true,null,["COMMENT"=>"дата входа"]));
        $table->addConstraint(
            new Ddl\Constraint\PrimaryKey(['id'])
        );
        $table->addConstraint(
            new Ddl\Index\Index(['temp_date'],"temp_date")
        );
        $table->addConstraint(
            new Ddl\Index\Index(['confirm_hash'],"confirm_hash")
        );
        $table->addConstraint(
            new Ddl\Index\Index(['status'],"status")
        );
        $table->addConstraint(
            new Ddl\Index\Index(['date_registration'],"date_registration")
        );
        $table->addConstraint(
            new Ddl\Index\Index(['login'],"login")
        );
        $this->mysql_add_create_table=" ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='регистрированные юзеры (база)'";
        $this->addSql($table);
        
        $insert = new Sql\Insert("users");
        $insert->columns(['id', 'login', 'status', 'password', 'name',"full_name"]);
        $insert->values([1,"root",3,'$2y$10$R4b7CZhWutZPDlNFoUF9Se/LVeHrWtsnRj4OM4HxC4yxc3scLObYW',"root","root"]);
        $this->addSql($insert);
        $insert->values([2,"guest",4,'$2y$10$R4b7CZhWutZPDlNFoUF9Se/LVeHrWtsnRj4OM4HxC4yxc3scLObYW',"guest","guest"]);
        $this->addSql($insert);
        $insert->values([11,"test",4,'$2y$10$R4b7CZhWutZPDlNFoUF9Se/LVeHrWtsnRj4OM4HxC4yxc3scLObYW',"guest","guest"]);
        $this->addSql($insert);

        $table = new Ddl\CreateTable("users_group");
        $table->addColumn(new Ddl\Column\Integer('id',false,null,["AUTO_INCREMENT"=>true]));
        $table->addColumn(new Ddl\Column\Char('name', 127,false,"",["COMMENT"=>"Имя группы"]));
        $table->addColumn(new Ddl\Column\Char('description', 255,false,"",["COMMENT"=>"Описание"]));
        $table->addConstraint(
            new Ddl\Constraint\PrimaryKey(['id'])
        );
        $this->mysql_add_create_table=" ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Группы'";
        $this->addSql($table);

        $insert = new Sql\Insert("users_group");
        $insert->columns(['id', 'name', 'description']);
        $insert->values([1,'Администраторы','Администратор - полный доступ ко всем ресурсам']);
        $this->addSql($insert);
        $insert->values([2,'Гости','Гостевой вход - группа с минимальным уровнем доступа']);
        $this->addSql($insert);
        $insert->values([6,'Редакторы','Редакторы информации']);
        $this->addSql($insert);
        $insert->values([7,'Регистрированные посетители','Регистрированные посетители']);
        $this->addSql($insert);
        
        $table = new Ddl\CreateTable("users2group");
        $table->addColumn(new Ddl\Column\Integer('users',false));
        $table->addColumn(new Ddl\Column\Integer('users_group',false));
        $table->addConstraint(
            new Ddl\Constraint\PrimaryKey(['users',"users_group"])
        );
        $table->addConstraint(
            new Ddl\Index\Index(['users'])
        );
        $table->addConstraint(
            new Ddl\Index\Index(["users_group"])
        );
        $table->addConstraint(
            new Ddl\Constraint\ForeignKey("users2group_fk","users","users","id","CASCADE")
        );
        $table->addConstraint(
            new Ddl\Constraint\ForeignKey("users2group_fk1","users_group","users_group","id","CASCADE")
        );
        $this->mysql_add_create_table=" ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='юзер<->группа'";
        $this->addSql($table);

        $insert = new Sql\Insert("users2group");
        $insert->columns(['users', 'users_group']);
        $insert->values([1,1]);
        $this->addSql($insert);
        $insert->values([2,2]);
        $this->addSql($insert);
        $insert->values([11,2]);
        $this->addSql($insert);

        $table = new Ddl\CreateTable("users_ext");
        $table->addColumn(new Ddl\Column\Integer('id',false));
        $table->addConstraint(
            new Ddl\Constraint\PrimaryKey(['id'])
        );
        $table->addConstraint(
            new Ddl\Constraint\ForeignKey("users_ext_fk","id","users","id","CASCADE")
        );
        $this->mysql_add_create_table=" ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='расширение юзеров'";
        $this->addSql($table);
        
        $insert = new Sql\Insert("users_ext");
        $insert->columns(['id']);
        $insert->values([1]);
        $this->addSql($insert);
        $insert->values([2]);
        $this->addSql($insert);
        $insert->values([11]);
        $this->addSql($insert);

        $table = new Ddl\CreateTable("users_group_tree");
        $table->addColumn(new Ddl\Column\Integer('id',false));
        $table->addColumn(new Ddl\Column\Integer('parent_id',false));
        
        $table->addConstraint(
            new Ddl\Constraint\PrimaryKey(['id',"parent_id"])
        );
        $table->addConstraint(
            new Ddl\Index\Index(['id'])
        );
        $table->addConstraint(
            new Ddl\Index\Index(['parent_id'])
        );
        $table->addConstraint(
            new Ddl\Constraint\ForeignKey("users_group_tree_fk","id","users_group","id","CASCADE")
        );
        $table->addConstraint(
            new Ddl\Constraint\ForeignKey("users_group_tree_fk1","parent_id","users_group","id","CASCADE")
        );
        $this->mysql_add_create_table=" ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='дерево групп'";
        $this->addSql($table);

    }

    public function down($schema, $adapter)
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
