<?php  
class Model_Add extends Orm\Model
{
	protected static $_table_name = 'add';
	protected static $_primary_key = array('id_list', 'id_piece');
	protected static $_properties = array(
        'id_list'=> array('data_type' => 'int'), 
        'id_piece' => array('data_type' => 'int')
    );
	
}