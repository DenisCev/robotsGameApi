<?php
namespace Fuel\Migrations;

class Pieces
{
    function up()
    {
        \DBUtil::create_table('pieces', array
            (
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'name' => array('type' => 'varchar', 'constraint' => 100),
            'side' => array('type' => 'varchar', 'constraint' => 100),
            'element' => array('type' => 'varchar', 'constraint' => 100),
            'rarity' => array('type' => 'varchar', 'constraint' => 100),
            'life' => array('type' => 'int', 'constraint' => 11),
            'damage' => array('type' => 'int', 'constraint' => 11),
            'speed' => array('type' => 'int', 'constraint' => 11),
            'cadence' => array('type' => 'int', 'constraint' => 11),
            'description' => array('type' => 'varchar', 'constraint' => 100, 'null' => true)
            ), array('id'));
    }

    function down()
    {
       \DBUtil::drop_table('pieces');
    }
}