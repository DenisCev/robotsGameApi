<?php
namespace Fuel\Migrations;

class Users
{
    function up()
    {
        \DBUtil::create_table('users', array
            (
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'name' => array('type' => 'varchar', 'constraint' => 100),
            'pass' => array('type' => 'varchar', 'constraint' => 255),
            'email' => array('type' => 'varchar', 'constraint' => 100),
            'urlPhoto' => array('type' => 'varchar', 'constraint' => 200, 'null' => true),
            'defeats' => array('type' => 'int', 'constraint' => 11),
            'victories' => array('type' => 'int', 'constraint' => 11)
            ), array('id'));

        \DBUtil::create_index('users',array('name','email'),'INDEX','UNIQUE');
    }

    function down()
    {
       \DBUtil::drop_table('users');
    }
}