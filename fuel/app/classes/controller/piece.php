<?php 
class Controller_Piece extends Controller_Base
{
	
    public function post_create()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
            	if(!isset($_POST['name']) || 
                    !isset($_POST['side']) || 
                    !isset($_POST['element']) ||
                    !isset($_POST['rarity']) || 
                    !isset($_POST['life']) || 
                    !isset($_POST['damage']) ||
                    !isset($_POST['speed']) || 
                    !isset($_POST['cadence'])
                    )
                {
                    return self::EmptyError();
                }

                $info = self::getUserInfo();

                if($info['name'] == self::DevName())
	            {
	                $input = $_POST;
	                
	                $pieces = Model_Pieces::find('all', array(
	                    'where' => array(
	                        array('name', $input['name'])
	                    )
	                ));

	                if(!empty($pieces))
	                {
	                    $response = $this->response(array(
	                        'code' => 400,
	                        'message' => 'Esa pieza ya esta registrada',
	                        'data' => ''
	                    ));
	                    return $response;
	                }

	                $piece = new Model_Pieces();
	                $piece->name = $input['name'];
	                $piece->side = $input['side'];
	                $piece->element = $input['element'];
	                $piece->rarity = $input['rarity'];
	                $piece->life = $input['life'];
	                $piece->damage = $input['damage'];
	                $piece->speed = $input['speed'];
	                $piece->cadence = $input['cadence'];

	                if(array_key_exists('description', $input))
	                {
	                    $piece->description = $input['description'];
	                }
	                $piece->save();

	                $response = $this->response(array(
	                    'code' => 200,
	                    'message' => 'Pieza creada',
	                    'data' => ''
	                ));
	                return $response;
	            }
	            else
	            {
	                return self::AuthError();
	            }
            }
            else
            {
            	return self::AuthError();
            }
        }
        catch (Exception $e)
        {
            return self::ServerError();
        }
    }

    public function post_createDefaultData()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
            	$info = self::getUserInfo();
            }
            else
            {
            	return self::AuthError();
            }

            if($info['name'] == self::DevName())
            { 
			    $query = DB::insert('pieces');
			    $query->set(array(
			        'name' => 'Strength Arm',
			        'side' => 'Left',
			        'element' => 'Strength',
			        'rarity' => 'Normal',
			        'life' => 1,
			        'damage' => 4,
			        'speed' => 1,
			        'cadence' => 2,
			        'description' => 'Puede tener o no'
			    ));
			    $query->execute();
			    $query = null;

			    $query = DB::insert('pieces');
			    $query->set(array(
			        'name' => 'Strength Arm',
			        'side' => 'Left',
			        'element' => 'Strength',
			        'rarity' => 'Rare',
			        'life' => 2,
			        'damage' => 7,
			        'speed' => 1,
			        'cadence' => 2,
			        'description' => 'Puede tener o no'
			    ));
			    $query->execute();
			    $query = null;
			    
			    $query = DB::insert('pieces');
			    $query->set(array(
			        'name' => 'Agility Arm',
			        'side' => 'Right',
			        'element' => 'Agility',
			        'rarity' => 'Normal',
			        'life' => 1,
			        'damage' => 1,
			        'speed' => 4,
			        'cadence' => 1,
			        'description' => 'Puede tener o no'
			    ));
			    $query->execute();
			    $query = null;

			    $query = DB::insert('pieces');
			    $query->set(array(
			        'name' => 'Junk01',
			        'side' => 'Right',
			        'element' => 'Defense',
			        'rarity' => 'Legendary',
			        'life' => 10,
			        'damage' => 2,
			        'speed' => 2,
			        'cadence' => 3,
			        'description' => 'La fortaleza de un veterano de guerra'
			    ));
			    $query->execute();
			    $query = null;

			    $query = DB::insert('pieces');
			    $query->set(array(
			        'name' => 'Defense Legs',
			        'side' => 'Down',
			        'element' => 'Defense',
			        'rarity' => 'Rare',
			        'life' => 6,
			        'damage' => 1,
			        'speed' => 2,
			        'cadence' => 2,
			        'description' => 'Puede tener o no'
			    ));
			    $query->execute();
			    $query = null;
			    
			    $query = DB::insert('pieces');
			    $query->set(array(
			        'name' => 'Agility Legs',
			        'side' => 'Down',
			        'element' => 'Agility',
			        'rarity' => 'Normal',
			        'life' => 1,
			        'damage' => 1,
			        'speed' => 4,
			        'cadence' => 1,
			        'description' => 'Puede tener o no'
			    ));
			    $query->execute();
			    

			    $response = $this->response(array(
                    'code' => 200,
                    'message' => 'Piezas creadas',
                    'data' => ''
                ));
                return $response;
            }
            else
            {
                return self::AuthError();
            }
        }
        catch (Exception $e)
        {
            return self::ServerError();
        }
    }

	public function post_delete()
	{
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
            	$info = self::getUserInfo();
            }
            else
            {
            	return self::AuthError();
            }

            if($info['name'] == self::DevName())
            {
                $input = $_POST;
                if(array_key_exists('id', $input))
                {
                    $piece = Model_Pieces::find($input['id']);
                    if(!empty($piece))
                    {
                        $piece->delete();
                        $response = $this->response(array(
                            'code' => 200,
                            'message' => 'Pieza borrada',
                            'data' => ''
                        ));
                        return $response;
                    }
                    else
                    {
                        $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Esa pieza no existe',
                        'data' => ''
                        ));
                        return $response;
                    }
                }
                else
                {	
                    return self::EmptyError();
                }  	
            }
            else
            {
                return self::AuthError();
            }
        }
        catch (Exception $e)
        {
            return self::ServerError();
        }
	}
}