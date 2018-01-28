<?php
namespace Fuel\Migrations;

class Add
{
    function up()
    {
        \DBUtil::create_table('add', array
            (
            'id_list' => array('type' => 'int', 'constraint' => 11),
            'id_piece' => array('type' => 'int', 'constraint' => 11),
            ), array('id_list' , 'id_piece'), true, 'InnoDB', 'utf8_general_ci',
            array(
                array(
                    'constraint' => 'foreingKeyAddToLists',
                    'key' => 'id_list',
                    'reference' => array(
                        'table' => 'lists',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE'
                ),
                array(
                    'constraint' => 'foreingKeyAddToPieces',
                    'key' => 'id_piece',
                    'reference' => array(
                        'table' => 'pieces',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE'
                )
            )
        );
    }

    function down()
    {
       \DBUtil::drop_table('add');
    }
}