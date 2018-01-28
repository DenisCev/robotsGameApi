<?php  
class Model_Users extends Orm\Model
{
	protected static $_table_name = 'users';
	protected static $_primary_key = array('id');
	protected static $_properties = array(
        'id'=> array('data_type' => 'int'),
        'name' => array('data_type' => 'varchar'),
        'email' => array('data_type' => 'varchar'),
        'pass' => array('data_type' => 'varchar'),
        'urlPhoto' => array('data_type' => 'varchar'),
    	'defeats' => array('data_type' => 'int'),
    	'victories' => array('data_type' => 'int')
    );
    
	protected static $_has_many = array(
	    'lists' => array(
	        'key_from' => 'id',
	        'model_to' => 'Model_Lists',
	        'key_to' => 'id_user',
	        'cascade_save' => true,
	        'cascade_delete' => true,
	    )
	);
}