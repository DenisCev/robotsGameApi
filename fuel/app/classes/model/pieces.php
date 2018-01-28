<?php  
class Model_Pieces extends Orm\Model
{
	protected static $_table_name = 'pieces';
	protected static $_primary_key = array('id');
	protected static $_properties = array(
        'id'=> array('data_type' => 'int'), 
        'name' => array('data_type' => 'varchar'),
        'side' => array('data_type' => 'varchar'),
        'element' => array('data_type' => 'varchar'),
        'rarity' => array('data_type' => 'varchar'),
        'life' => array('data_type' => 'int'),
        'damage' => array('data_type' => 'int'),
        'speed' => array('data_type' => 'int'),
        'cadence' => array('data_type' => 'int'),
        'description' => array('data_type' => 'varchar')
    );
	
    protected static $_many_many = array(
	    'lists' => array(
	        'key_from' => 'id',
	        'key_through_from' => 'id_list',
	        'table_through' => 'add',
	        'key_through_to' => 'id_piece',
	        'model_to' => 'Model_Lists',
	        'key_to' => 'id',
	        'cascade_save' => true,
	        'cascade_delete' => false,
	    )
	);
}